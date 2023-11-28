<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\Type;

use Oksydan\IsMainMenu\Entity\MenuELement;
use Oksydan\IsMainMenu\Form\ChoiceProvider\CMSPagesChoiceProvider;
use Oksydan\IsMainMenu\Form\ChoiceProvider\MenuLayoutGridChoiceProvider;
use Oksydan\IsMainMenu\Form\ChoiceProvider\MenuTypeChoiceProvider;
use Oksydan\IsMainMenu\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\ImagePreviewType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuElementType extends TranslatorAwareType
{
    /**
     * Multistore feature activated
     *
     * @var bool
     */
    private MultistoreFeature $multistoreFeature;

    /*
     * @var MenuTypeChoiceProvider
     */
    private MenuTypeChoiceProvider $menuTypeChoiceProvider;

    /*
     * @var CMSPagesChoiceProvider
     */
    private CMSPagesChoiceProvider $cmsPagesChoiceProvider;

    /*
     * @var MenuLayoutGridChoiceProvider
     */
    private MenuLayoutGridChoiceProvider $menuLayoutGridChoiceProvider;

    /*
     * @var RouterInterface
     */
    private RouterInterface $router;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        MultistoreFeature $multistoreFeature,
        MenuTypeChoiceProvider $menuTypeChoiceProvider,
        CMSPagesChoiceProvider $cmsPagesChoiceProvider,
        MenuLayoutGridChoiceProvider $menuLayoutGridChoiceProvider,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);

        $this->multistoreFeature = $multistoreFeature;
        $this->menuTypeChoiceProvider = $menuTypeChoiceProvider;
        $this->cmsPagesChoiceProvider = $cmsPagesChoiceProvider;
        $this->menuLayoutGridChoiceProvider = $menuLayoutGridChoiceProvider;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = !empty($options['data']['id_menu_element']);

        $builder = $this->buildDefaultFormFields($builder, $options);

        if ($isEdit) {
            switch ($options['data']['type']) {
                case MenuElement::TYPE_CATEGORY:
                    $builder = $this->buildCategoryType($builder, $options);
                    break;
                case MenuElement::TYPE_LINK:
                    $builder = $this->buildCustomLinkType($builder, $options);
                    break;
                case MenuElement::TYPE_BANNER:
                    $builder = $this->buildBannerType($builder, $options);
                    break;
                case MenuElement::TYPE_HTML:
                    $builder = $this->buildHtmlType($builder, $options);
                    break;
                case MenuElement::TYPE_CMS:
                    $builder = $this->buildCMSType($builder, $options);
                    break;
                case MenuElement::TYPE_PRODUCT:
                    $builder = $this->buildProductType($builder, $options);
                    break;
                default:
                    throw new \Exception('Unknown type: ' . $options['data']['menu_element']['type'] . ' for menu element');
            }
        }

        if ($this->multistoreFeature->isActive()) {
            $builder->add(
                'shop_association',
                ShopChoiceTreeType::class,
                [
                    'label' => $this->trans('Shop associations', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'You have to select at least one shop to associate this item with',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ]
            );
        }
    }

    private function buildCustomLinkType(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $builder
            ->add('custom_name', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Link title', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => true,
            ])
            ->add('url', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Link url', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => true,
            ]);

        return $builder;
    }

    private function buildCategoryType(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $builder
            ->add('custom_name', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Link title', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => true,
            ])
            ->add('id_category', CategoryChoiceTreeType::class, [
                'label' => $this->trans('Select category', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
            ]);

        return $builder;
    }

    private function buildBannerType(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $imageConstrains = [
            new File([
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                ],
            ]),
        ];

        $builder
            ->add('custom_name', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Banner title', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => true,
            ])
            ->add('alt', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Banner alt', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => true,
            ])
            ->add('image_preview', TranslatableType::class, [
                'type' => ImagePreviewType::class,
                'required' => false,
                'label' => $this->trans('Banner image preview', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
            ])
            ->add('image', TranslatableType::class, [
                'type' => FileType::class,
                'label' => $this->trans('Banner image', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'options' => [
                    'data_class' => null,
                    'constraints' => $imageConstrains,
                ],
                'required' => false,
            ])
            ->add('url', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Link url', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => true,
            ]);

        return $builder;
    }

    private function buildHtmlType(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $builder
            ->add('custom_name', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Content title', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => true,
            ])
            ->add('content', TranslatableType::class, [
                'type' => FormattedTextareaType::class,
                'label' => $this->trans('Content', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => true,
            ]);

        return $builder;
    }

    private function buildCMSType(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $builder
            ->add('custom_name', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Content title', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => true,
            ])
            ->add('id_cms', ChoiceType::class, [
                'required' => true,
                'label' => $this->trans('CMS page', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'choices' => $this->cmsPagesChoiceProvider->getChoices(),
            ]);

        return $builder;
    }

    private function buildProductType(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $builder
            ->add('product', ProductAutocompleteType::class, [
                'label' => $this->trans('Product autocomplete', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
                'autocomplete_url' => $this->router->generate('is_mainmenu_api_controller_product_autocomplete'),
                'selected_product_url' => $this->router->generate('is_mainmenu_api_controller_product_selected'),
            ]);

        return $builder;
    }

    private function buildDefaultFormFields(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $isEdit = !empty($options['data']['id_menu_element']);

        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Private menu element name', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'help' => $this->trans('Name used only for block identification in BO', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
            ])
            ->add('display_desktop', SwitchType::class, [
                'label' => $this->trans('Display in desktop menu', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
            ])
            ->add('display_mobile', SwitchType::class, [
                'label' => $this->trans('Display in mobile menu', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
            ])
            ->add('css_class', TextType::class, [
                'label' => $this->trans('Css classes for this element', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'help' => $this->trans('Extra css class that will be added to menu item', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => false,
            ])
            ->add('grid_type', ChoiceType::class, [
                'required' => true,
                'label' => $this->trans('Menu desktop bootstrap grid', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'help' => $this->trans('It\'s only working menu element with depth > 1', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'choices' => $this->menuLayoutGridChoiceProvider->getChoices(),
            ]);

        if (!empty($options['data']['id_parent_element']) && $options['data']['id_parent_element']) {
            $builder->add('id_parent_element', HiddenType::class, [
                'required' => true,
                'data' => $options['data']['id_parent_element'],
            ]);
        }

        if ($isEdit) {
            $builder->add('type', ChoiceType::class, [
                'required' => true,
                'label' => $this->trans('Menu element type', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'help' => $this->trans('You can\'t change element type', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'attr' => [
                    'disabled' => 'disabled',
                ],
                'choices' => $this->menuTypeChoiceProvider->getChoices(),
            ]);
        } else {
            $builder->add('type', ChoiceType::class, [
                'required' => true,
                'label' => $this->trans('Menu element type', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'choices' => $this->menuTypeChoiceProvider->getChoices(),
            ]);
        }

        return $builder;
    }
}

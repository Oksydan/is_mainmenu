<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\Type;

use Oksydan\IsMainMenu\Form\AutocompleteRender\AutocompleteSelectedProductRender;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Oksydan\IsMainMenu\Form\Type\DataTransformer\ProductAutocompleteDataTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductAutocompleteType extends AbstractType
{
    /*
     * @var AutocompleteSelectedProductRender
     */
    private AutocompleteSelectedProductRender $autocompleteSelectedProductRender;

    public function __construct(
        AutocompleteSelectedProductRender $autocompleteSelectedProductRender,
    ) {
        $this->autocompleteSelectedProductRender = $autocompleteSelectedProductRender;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ProductAutocompleteDataTransformer(
            $this->autocompleteSelectedProductRender
        ));

        $builder
            ->add('product_query', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'js-product-autocomplete-input',
                    'data-autocomplete-url' => $options['autocomplete_url'],
                    'data-selected-product-url' => $options['selected_product_url'],

                    // To prevent browser autocomplete
                    'role' => 'presentation',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('id_product', HiddenType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'js-id-product',
                ],
            ])
            ->add('id_product_attribute', HiddenType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'js-id-product-attribute',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined([
            'autocomplete_url',
            'selected_product_url',
        ]);

        $resolver->setAllowedTypes('autocomplete_url', 'string');
        $resolver->setAllowedTypes('selected_product_url', 'string');
        $resolver->setRequired('autocomplete_url');
        $resolver->setRequired('selected_product_url');
    }
}

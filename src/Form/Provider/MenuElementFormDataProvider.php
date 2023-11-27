<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\Provider;

use Is_mainmenu;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementBanner;
use Oksydan\IsMainMenu\Entity\MenuElementCategory;
use Oksydan\IsMainMenu\Entity\MenuElementCms;
use Oksydan\IsMainMenu\Entity\MenuElementCustom;
use Oksydan\IsMainMenu\Entity\MenuElementHtml;
use Oksydan\IsMainMenu\Entity\MenuElementProduct;
use Oksydan\IsMainMenu\Repository\MenuElementBannerRepository;
use Oksydan\IsMainMenu\Repository\MenuElementCategoryRepository;
use Oksydan\IsMainMenu\Repository\MenuElementCmsRepository;
use Oksydan\IsMainMenu\Repository\MenuElementCustomRepository;
use Oksydan\IsMainMenu\Repository\MenuElementHtmlRepository;
use Oksydan\IsMainMenu\Repository\MenuElementProductRepository;
use Oksydan\IsMainMenu\Repository\MenuElementRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

class MenuElementFormDataProvider implements FormDataProviderInterface
{
    /*
     * @var MenuElementRepository
     */
    private MenuElementRepository $menuElementRepository;

    /*
     * @var MenuElementCustomRepository
     */
    private MenuElementCustomRepository $menuElementCustomRepository;

    /*
     * @var MenuElementHtmlRepository
     */
    private MenuElementHtmlRepository $menuElementHtmlRepository;

    /*
     * @var MenuElementCategoryRepository
     */
    private MenuElementCategoryRepository $menuElementCategoryRepository;

    /*
     * @var MenuElementBannerRepository
     */
    private MenuElementBannerRepository $menuElementBannerRepository;

    /*
     * @var MenuElementCmsRepository
     */
    private MenuElementCmsRepository $menuElementCmsRepository;

    /*
     * @var MenuElementProductRepository
     */
    private MenuElementProductRepository $menuElementProductRepository;

    /*
     * @var Is_mainmenu
     */
    private \Is_mainmenu $module;

    /*
     * @var Context
     */
    private Context $contextAdapter;

    public function __construct(
        MenuElementRepository $menuElementRepository,
        MenuElementCustomRepository $menuElementCustomRepository,
        MenuElementHtmlRepository $menuElementHtmlRepository,
        MenuElementCategoryRepository $menuElementCategoryRepository,
        MenuElementBannerRepository $menuElementBannerRepository,
        MenuElementCmsRepository $menuElementCmsRepository,
        MenuElementProductRepository $menuElementProductRepository,
        \Is_mainmenu $module,
        Context $contextAdapter
    ) {
        $this->menuElementRepository = $menuElementRepository;
        $this->menuElementCustomRepository = $menuElementCustomRepository;
        $this->menuElementHtmlRepository = $menuElementHtmlRepository;
        $this->menuElementCategoryRepository = $menuElementCategoryRepository;
        $this->menuElementBannerRepository = $menuElementBannerRepository;
        $this->menuElementCmsRepository = $menuElementCmsRepository;
        $this->menuElementProductRepository = $menuElementProductRepository;
        $this->module = $module;
        $this->contextAdapter = $contextAdapter;
    }

    public function getData($id)
    {
        $menuElement = $this->menuElementRepository->find($id);
        $shopIds = [];

        foreach ($menuElement->getShops() as $shop) {
            $shopIds[] = $shop->getId();
        }

        $data = [
            'shop_association' => $shopIds,
            'id_menu_element' => $menuElement->getId(),
            'name' => $menuElement->getName(),
            'active' => $menuElement->getActive(),
            'css_class' => $menuElement->getCssClass(),
            'type' => $menuElement->getType(),
            'display_desktop' => $menuElement->getDisplayDesktop(),
            'display_mobile' => $menuElement->getDisplayMobile(),
            'position' => $menuElement->getPosition(),
        ];

        $parentMenuElement = $menuElement->getParentMenuElement();

        if ($parentMenuElement instanceof MenuElement) {
            $data['id_parent_element'] = $parentMenuElement->getId();
        }

        switch ($menuElement->getType()) {
            case MenuElement::TYPE_LINK:
                $data = array_merge($data, $this->getCustomElementData($id));
                break;
            case MenuElement::TYPE_CATEGORY:
                $data = array_merge($data, $this->getCategoryElementData($id));
                break;
            case MenuElement::TYPE_BANNER:
                $data = array_merge($data, $this->getBannerElementData($id));
                break;
            case MenuElement::TYPE_HTML:
                $data = array_merge($data, $this->getHtmlElementData($id));
                break;
            case MenuElement::TYPE_CMS:
                $data = array_merge($data, $this->getCMSElementData($id));
                break;
            case MenuElement::TYPE_PRODUCT:
                $data = array_merge($data, $this->getProductElementData($id));
                break;
        }

        return $data;
    }

    private function getProductElementData($id): array
    {
        $data = [];
        $menuElementProduct = $this->menuElementProductRepository->findOneBy([
            'menuElement' => $id,
        ]);

        if ($menuElementProduct instanceof MenuElementProduct) {
            $data['product'] = [
                'id_product' => $menuElementProduct->getIdProduct(),
                'id_product_attribute' => $menuElementProduct->getIdProductAttribute(),
            ];
        }

        return $data;
    }

    private function getHtmlElementData($id): array
    {
        $data = [];
        $menuElementHtml = $this->menuElementHtmlRepository->findOneBy([
            'menuElement' => $id,
        ]);

        if ($menuElementHtml instanceof MenuElementHtml) {
            $menuElementHtmlLang = $menuElementHtml->getMenuElementHtmlLangs();
            $data['custom_name'] = [];
            $data['content'] = [];

            foreach ($menuElementHtmlLang as $element) {
                $lang = $element->getLang();

                $data['custom_name'][$lang->getId()] = $element->getName();
                $data['content'][$lang->getId()] = $element->getContent();
            }
        }

        return $data;
    }

    private function getCMSElementData($id): array
    {
        $data = [];
        $menuElementCms = $this->menuElementCmsRepository->findOneBy([
            'menuElement' => $id,
        ]);

        if ($menuElementCms instanceof MenuElementCms) {
            $menuElementCmsLang = $menuElementCms->getMenuElementCmsLangs();
            $data['id_cms'] = $menuElementCms->getIdCMS();
            $data['custom_name'] = [];

            foreach ($menuElementCmsLang as $element) {
                $lang = $element->getLang();

                $data['custom_name'][$lang->getId()] = $element->getName();
            }
        }

        return $data;
    }

    private function buildImagePreviewPath(string $imageFilename): string
    {
        return $this->module->getImagePath($imageFilename);
    }

    private function getImagePlaceholder(): string
    {
        return $this->module->getImagePath('placeholder.jpeg');
    }

    private function getBannerElementData($id): array
    {
        $data = [];
        $menuElementBanner = $this->menuElementBannerRepository->findOneBy([
            'menuElement' => $id,
        ]);

        if ($menuElementBanner instanceof MenuElementBanner) {
            $menuElementBannerLang = $menuElementBanner->getMenuElementBannerLangs();
            $data['custom_name'] = [];
            $data['alt'] = [];
            $data['url'] = [];
            $data['image_preview'] = [];

            foreach ($menuElementBannerLang as $element) {
                $lang = $element->getLang();

                $data['custom_name'][$lang->getId()] = $element->getName();
                $data['alt'][$lang->getId()] = $element->getAlt();
                $data['url'][$lang->getId()] = $element->getUrl();

                if ($element->getFilename() !== null) {
                    $data['image_preview'][$lang->getId()] = $this->buildImagePreviewPath($element->getFilename());
                } else {
                    $data['image_preview'][$lang->getId()] = $this->getImagePlaceholder();
                }
            }
        }

        return $data;
    }

    private function getCategoryElementData($id): array
    {
        $data = [];
        $menuElementCategory = $this->menuElementCategoryRepository->findOneBy([
            'menuElement' => $id,
        ]);

        if ($menuElementCategory instanceof MenuElementCategory) {
            $menuElementCategoryLang = $menuElementCategory->getMenuElementCategoryLangs();
            $data['custom_name'] = [];
            $data['id_category'] = $menuElementCategory->getIdCategory();

            foreach ($menuElementCategoryLang as $element) {
                $lang = $element->getLang();

                $data['custom_name'][$lang->getId()] = $element->getName();
            }
        }

        return $data;
    }

    private function getCustomElementData($id): array
    {
        $data = [];
        $menuElementCustom = $this->menuElementCustomRepository->findOneBy([
            'menuElement' => $id,
        ]);

        if ($menuElementCustom instanceof MenuElementCustom) {
            $menuElementCustomLangs = $menuElementCustom->getMenuElementCustomLangs();
            $data['custom_name'] = [];
            $data['url'] = [];

            foreach ($menuElementCustomLangs as $element) {
                $lang = $element->getLang();

                $data['custom_name'][$lang->getId()] = $element->getName();
                $data['url'][$lang->getId()] = $element->getUrl();
            }
        }

        return $data;
    }

    public function getDefaultData(): array
    {
        return [
            'name' => '',
            'active' => false,
            'css_class' => '',
            'type' => null,
            'display_desktop' => true,
            'display_mobile' => true,
            'position' => 0,
            'id_parent_element' => $this->menuElementRepository->getRootElement()->getId(),
            'shop_association' => $this->contextAdapter->getContextListShopID(),
            'product' => [
                'id_product' => 0,
                'id_product_attribute' => 0,
            ],
        ];
    }
}

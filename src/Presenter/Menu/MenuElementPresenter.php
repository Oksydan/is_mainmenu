<?php

namespace Oksydan\IsMainMenu\Presenter\Menu;

use Is_mainmenu;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementBanner;
use Oksydan\IsMainMenu\Entity\MenuElementCategory;
use Oksydan\IsMainMenu\Entity\MenuElementCms;
use Oksydan\IsMainMenu\Entity\MenuElementCustom;
use Oksydan\IsMainMenu\Entity\MenuElementHtml;
use Oksydan\IsMainMenu\Entity\MenuElementProduct;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;
use Oksydan\IsMainMenu\Menu\MenuElementRelatedElementProvider;
use Oksydan\IsMainMenu\Menu\MenuElementVisibilityManager;
use Oksydan\IsMainMenu\Presenter\Product\ProductFrontPresenter;
use Oksydan\IsMainMenu\Repository\MenuElementRepository;

class MenuElementPresenter implements MenuElementPresenterInterface
{
    /*
     * @var \Context
     */
    private \Context $context;

    /*
     * @var Is_mainmenu
     */
    private \Is_mainmenu $module;

    /*
     * @var MenuElementRepository
     */
    private MenuElementRepository $menuElementRepository;

    /*
     * @var ProductFrontPresenter
     */
    private ProductFrontPresenter $productFrontPresenter;

    /**
     * @var MenuElementVisibilityManager
     */
    private MenuElementVisibilityManager $menuElementVisibilityManager;

    /*
     * @var MenuElementRelatedElementProvider
     */
    private MenuElementRelatedElementProvider $menuElementRelatedElementProvider;

    public function __construct(
        \Context $context,
        \Is_mainmenu $module,
        MenuElementRepository $menuElementRepository,
        ProductFrontPresenter $productFrontPresenter,
        MenuElementVisibilityManager $menuElementVisibilityManager,
        MenuElementRelatedElementProvider $menuElementRelatedElementProvider
    ) {
        $this->context = $context;
        $this->module = $module;
        $this->menuElementRepository = $menuElementRepository;
        $this->productFrontPresenter = $productFrontPresenter;
        $this->menuElementVisibilityManager = $menuElementVisibilityManager;
        $this->menuElementRelatedElementProvider = $menuElementRelatedElementProvider;
    }

    public function present(MenuElement $menuElement, MenuElementRelatedEntityInterface $relatedMenuElement): array
    {
        $elementPresented = $this->assignDefaultData($menuElement);

        switch ($menuElement->getType()) {
            case MenuElement::TYPE_CATEGORY:
                $elementPresented = array_merge($elementPresented, $this->assignCategoryData($relatedMenuElement));
                break;
            case MenuElement::TYPE_HTML:
                $elementPresented = array_merge($elementPresented, $this->assignHtmlData($relatedMenuElement));
                break;
            case MenuElement::TYPE_BANNER:
                $elementPresented = array_merge($elementPresented, $this->assignBannerData($relatedMenuElement));
                break;
            case MenuElement::TYPE_LINK:
                $elementPresented = array_merge($elementPresented, $this->assignCustomData($relatedMenuElement));
                break;
            case MenuElement::TYPE_CMS:
                $elementPresented = array_merge($elementPresented, $this->assignCmsData($relatedMenuElement));
                break;
            case MenuElement::TYPE_PRODUCT:
                $elementPresented = array_merge($elementPresented, $this->assignProductData($relatedMenuElement));
                break;
        }

        return $elementPresented;
    }

    private function assignCategoryData(MenuElementCategory $menuElementCategory): array
    {
        $menuElementCategoryLang = $menuElementCategory->getMenuElementCategoryLangsByLangId($this->context->language->id);

        return [
            'title' => $menuElementCategoryLang->getName(),
            'url' => $this->context->link->getCategoryLink($menuElementCategory->getIdCategory()),
        ];
    }

    private function assignHtmlData(MenuElementHtml $menuElementHtml): array
    {
        $menuElementHtmlLang = $menuElementHtml->getMenuElementHtmlLangsByLangId($this->context->language->id);

        return [
            'title' => $menuElementHtmlLang->getName(),
            'content' => $menuElementHtmlLang->getContent(),
        ];
    }

    private function assignBannerData(MenuElementBanner $menuElementBanner): array
    {
        $menuElementBannerLang = $menuElementBanner->getMenuElementBannerLangsByLangId($this->context->language->id);

        $image = $menuElementBannerLang->getFilename();
        $imageData = [];

        if ($image) {
            $imageAbsolute = $this->module->getImageAbsoluteDir($image);

            if (@file_exists($imageAbsolute)) {
                [$width, $height] = getimagesize($imageAbsolute);
                $imageData['imageSrc'] = $this->module->getImagePath($image);
                $imageData['imageSizes'] = [
                    'width' => $width,
                    'height' => $height,
                ];
                $imageData['alt'] = $menuElementBannerLang->getALt();
            }
        }

        return [
            'title' => $menuElementBannerLang->getName(),
            'url' => $menuElementBannerLang->getUrl(),
            'banner' => !empty($imageData) ? $imageData : null,
        ];
    }

    private function assignCustomData(MenuElementCustom $menuElementCustom): array
    {
        $menuElementCustomLang = $menuElementCustom->getMenuElementCustomLangsByLangId($this->context->language->id);

        return [
            'title' => $menuElementCustomLang->getName(),
            'url' => $menuElementCustomLang->getUrl(),
        ];
    }

    private function assignCmsData(MenuElementCms $menuElementCms): array
    {
        $menuElementCmsLang = $menuElementCms->getMenuElementCmsLangsByLangId($this->context->language->id);

        return [
            'url' => $this->context->link->getCMsLink($menuElementCms->getIdCMS()),
            'title' => $menuElementCmsLang->getName(),
        ];
    }

    private function assignProductData(MenuElementProduct $menuElementProduct): array
    {
        $idProduct = $menuElementProduct->getIdProduct();
        $idProductAttribute = $menuElementProduct->getIdProductAttribute();

        $productPresented = $this->productFrontPresenter->present([
            'id_product' => $idProduct,
            'id_product_attribute' => $idProductAttribute,
        ]);

        return [
            'product' => $productPresented,
        ];
    }

    private function hasChildren(MenuElement $menuElement): bool
    {
        $children = $this->menuElementRepository->getActiveMenuElementChildrenByStoreId($menuElement, $this->context->shop->id);
        $children = array_filter($children, function (MenuElement $menuElement) {
            $relatedElement = $this->menuElementRelatedElementProvider->getRelatedMenuElementByMenuElement($menuElement);

            if (!$relatedElement) {
                return false;
            }

            return $this->menuElementVisibilityManager->shouldBeElementDisplayed($relatedElement);
        });

        return !empty($children);
    }

    private function assignDefaultData(MenuElement $menuElement): array
    {
        return [
            'id' => $menuElement->getId(),
            'type' => $menuElement->getType(),
            'css_class' => $menuElement->getCssClass(),
            'depth' => $menuElement->getDepth(),
            'has_children' => $this->hasChildren($menuElement),
        ];
    }
}

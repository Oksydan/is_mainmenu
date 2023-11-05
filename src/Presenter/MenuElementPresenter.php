<?php

namespace Oksydan\IsMainMenu\Presenter;

use Is_mainmenu;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementBanner;
use Oksydan\IsMainMenu\Entity\MenuElementCategory;
use Oksydan\IsMainMenu\Entity\MenuElementCms;
use Oksydan\IsMainMenu\Entity\MenuElementCustom;
use Oksydan\IsMainMenu\Entity\MenuElementHtml;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;

class MenuElementPresenter implements PresenterInterface
{
    /*
     * @var \Context
     */
    private \Context $context;

    /*
     * @var Is_mainmenu
     */
    private \Is_mainmenu $module;

    public function __construct(
        \Context $context,
        \Is_mainmenu $module
    ) {
        $this->context = $context;
        $this->module = $module;
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

    private function assignCmsData(MenuElementCms $menuElementCms)
    {
        $menuElementCmsLang = $menuElementCms->getMenuElementCmsLangsByLangId($this->context->language->id);

        return [
            'url' => $this->context->link->getCMsLink($menuElementCms->getIdCMS()),
            'title' => $menuElementCmsLang->getName(),
        ];
    }

    private function assignDefaultData(MenuElement $menuElement)
    {
        return [
            'id' => $menuElement->getId(),
            'type' => $menuElement->getType(),
            'css_class' => $menuElement->getCssClass(),
            'depth' => $menuElement->getDepth(),
        ];
    }

}

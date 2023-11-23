<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Handler\MenuElement;

use Doctrine\ORM\EntityManagerInterface;
use Oksydan\IsMainMenu\Cache\ModuleCache;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementBanner;
use Oksydan\IsMainMenu\Factory\FileEraserFactory;
use Oksydan\IsMainMenu\Repository\MenuElementBannerRepository;
use Oksydan\IsMainMenu\Repository\MenuElementCategoryRepository;
use Oksydan\IsMainMenu\Repository\MenuElementCustomRepository;
use Oksydan\IsMainMenu\Repository\MenuElementHtmlRepository;
use Oksydan\IsMainMenu\Repository\MenuElementRepository;

class DeleteMenuElementHandler implements MenuElementHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var MenuElementRepository
     */
    private MenuElementRepository $menuElementRepository;

    /*
     * @var MenuElementHtmlRepository
     */
    private MenuElementHtmlRepository $menuElementHtmlRepository;

    /*
     * @var MenuElementBannerRepository
     */
    private MenuElementBannerRepository $menuElementBannerRepository;

    /*
     * @var MenuElementCategoryRepository
     */
    private MenuElementCategoryRepository $menuElementCategoryRepository;

    /*
     * @var MenuElementCustomRepository
     */
    private MenuElementCustomRepository $menuElementCustomRepository;

    /*
     * @var FileEraserFactory
     */
    private FileEraserFactory $fileEraserFactory;

    /*
     * @var ModuleCache
     */
    private ModuleCache $moduleCache;
    public function __construct(
        EntityManagerInterface $entityManager,
        MenuElementRepository $menuElementRepository,
        MenuElementHtmlRepository $menuElementHtmlRepository,
        MenuElementBannerRepository $menuElementBannerRepository,
        MenuElementCategoryRepository $menuElementCategoryRepository,
        MenuElementCustomRepository $menuElementCustomRepository,
        FileEraserFactory $fileEraserFactory,
        ModuleCache $moduleCache,
    ) {
        $this->entityManager = $entityManager;
        $this->menuElementRepository = $menuElementRepository;
        $this->menuElementHtmlRepository = $menuElementHtmlRepository;
        $this->menuElementBannerRepository = $menuElementBannerRepository;
        $this->menuElementCategoryRepository = $menuElementCategoryRepository;
        $this->menuElementCustomRepository = $menuElementCustomRepository;
        $this->fileEraserFactory = $fileEraserFactory;
        $this->moduleCache = $moduleCache;
    }

    public function handle(int $menuElementId): void
    {
        $menuElement = $this->menuElementRepository->find($menuElementId);

        if ($menuElement) {
            $this->removeRecursively($menuElement);
            $this->removeMenuElement($menuElement);
            $this->entityManager->flush();

            $this->moduleCache->clearCache();
        }
    }

    private function removeRecursively(MenuElement $menuElement): void
    {
        $children = $this->menuElementRepository->findElementChildren($menuElement);

        foreach ($children as $child) {
            $this->removeRecursively($child);
            $this->removeMenuElement($child);
        }

        $this->removeMenuElement($menuElement);
    }

    private function removeMenuElement(MenuElement $menuElement): void
    {
        $relatedElement = null;

        switch ($menuElement->getType()) {
            case MenuElement::TYPE_HTML:
                $relatedElement = $this->menuElementHtmlRepository->findMenuElementHtmlByMenuElement($menuElement);
                break;
            case MenuElement::TYPE_BANNER:
                $relatedElement = $this->menuElementBannerRepository->findMenuElementBannerByMenuElement($menuElement);
                break;
            case MenuElement::TYPE_LINK:
                $relatedElement = $this->menuElementCustomRepository->findMenuElementCustomByMenuElement($menuElement);
                break;
            case MenuElement::TYPE_CATEGORY:
                $relatedElement = $this->menuElementCategoryRepository->findMenuElementCategoryByMenuElement($menuElement);
                break;
        }

        if ($relatedElement instanceof MenuElementBanner) {
            $this->handleErasingBannerFiles($relatedElement);
        }

        if ($relatedElement) {
            $this->entityManager->remove($relatedElement);
        }

        $this->entityManager->remove($menuElement);
    }

    private function handleErasingBannerFiles(MenuElementBanner $bannerElement): void
    {
        $bannersLangs = $bannerElement->getMenuElementBannerLangs();

        foreach ($bannersLangs as $bannerLang) {
            $fileName = $bannerLang->getFileName();

            if ($fileName) {
                $handler = $this->fileEraserFactory->create(FileEraserFactory::IMAGE_DIR);
                $handler->remove($fileName);
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Handler\MenuElement;

use Doctrine\ORM\EntityManagerInterface;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementCategory;
use Oksydan\IsMainMenu\Entity\MenuElementCategoryLang;
use Oksydan\IsMainMenu\LegacyRepository\CategoryLegacyRepository;
use Oksydan\IsMainMenu\Repository\MenuElementCategoryRepository;
use Oksydan\IsMainMenu\Repository\MenuElementRepository;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Entity\Shop;

class GenerateMenuCategoryTreeHandler implements MenuElementHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var MenuElementRepository
     */
    private MenuElementRepository $menuElementRepository;

    /**
     * @var DeleteMenuElementHandler
     */
    private DeleteMenuElementHandler $deleteMenuElementHandler;

    /**
     * @var MenuElementCategoryRepository
     */
    private MenuElementCategoryRepository $menuElementCategoryRepository;

    /**
     * @var CategoryLegacyRepository
     */
    private CategoryLegacyRepository $categoryLegacyRepository;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var LangRepository
     */
    private LangRepository $langRepository;

    /**
     * @var int
     */
    private $defaultLanguageId;

    /**
     * @var array [id_category => [id_lang => name]]
     */
    private array $categoryLangCache = [];

    /**
     * @var array [id_lang => Lang]
     */
    private array $langEntityCache = [];

    /**
     * @var array
     */
    private array $languages;

    public function __construct(
        EntityManagerInterface $entityManager,
        MenuElementRepository $menuElementRepository,
        DeleteMenuElementHandler $deleteMenuElementHandler,
        MenuElementCategoryRepository $menuElementCategoryRepository,
        CategoryLegacyRepository $categoryLegacyRepository,
        Configuration $configuration,
        LangRepository $langRepository,
        array $languages
    ) {
        $this->entityManager = $entityManager;
        $this->menuElementRepository = $menuElementRepository;
        $this->deleteMenuElementHandler = $deleteMenuElementHandler;
        $this->menuElementCategoryRepository = $menuElementCategoryRepository;
        $this->categoryLegacyRepository = $categoryLegacyRepository;
        $this->configuration = $configuration;
        $this->langRepository = $langRepository;
        $this->languages = $languages;
    }

    public function handle(int $menuElementId): void
    {
        $menuElement = $this->menuElementRepository->find($menuElementId);

        if ($menuElement && $menuElementCategory = $this->menuElementCategoryRepository->findOneBy(['menuElement' => $menuElement])) {
            $this->eraseChildrenElementsIfExists($menuElement);
            $this->buildCategoryTree($menuElement, $menuElementCategory);

            $this->entityManager->flush();
        }
    }

    private function eraseChildrenElementsIfExists(MenuElement $menuElement): void
    {
        $children = $this->menuElementRepository->findElementChildren($menuElement);

        if ($children) {
            foreach ($children as $child) {
                $this->deleteMenuElementHandler->handle($child->getId());
            }
        }
    }

    private function buildCategoryTree(MenuElement $menuElement, MenuElementCategory $menuElementCategory)
    {
        $this->buildMenuLinks($menuElement, $menuElementCategory);
    }

    private function buildMenuLinks(MenuElement $menuElement, MenuElementCategory $menuElementCategory)
    {
        $categoryTree = $this->getCategoryChildren($menuElementCategory->getIdCategory());

        $position = 0;

        foreach ($categoryTree as $category) {
            $newMenuElement = $this->persistMenuElement($menuElement, $position, $category['id_category']);
            $newMenuElementCategory = $this->persistMenuElementCategory($newMenuElement, $category['id_category']);

            ++$position;

            $this->buildMenuLinks($newMenuElement, $newMenuElementCategory);
        }
    }

    private function getCategoryNameByLangId($idCategory, $idLang)
    {
        if (empty($this->categoryLangCache[$idCategory])) {
            $this->categoryLangCache[$idCategory] = $this->categoryLegacyRepository->getCategoryLangArray($idCategory);
        }

        if (!empty($this->categoryLangCache[$idCategory][$idLang])) {
            return $this->categoryLangCache[$idCategory][$idLang];
        }

        return $this->categoryLangCache[$idCategory][$this->getDefaultLanguageId()];
    }

    private function persistMenuElement(MenuELement $parentMenuElement, $position, $categoryId): MenuElement
    {
        $newMenuElement = new MenuElement();
        $newMenuElement->setParentMenuElement($parentMenuElement);
        $newMenuElement->setActive(true);
        $newMenuElement->setType(MenuElement::TYPE_CATEGORY);
        $newMenuElement->setPosition($position);
        $newMenuElement->setDisplayMobile(true);
        $newMenuElement->setDisplayDesktop(true);
        $newMenuElement->setDepth($parentMenuElement->getDepth() + 1);
        $newMenuElement->setName($this->getCategoryNameByLangId($categoryId, $this->getDefaultLanguageId()));
        $newMenuElement->setCssClass('');
        $enabledStores = $this->categoryLegacyRepository->getEnabledStoresForCategory($categoryId);

        foreach ($enabledStores as $shop) {
            $shop = $this->entityManager->getRepository(Shop::class)->find((int) $shop['id_shop']);
            $newMenuElement->addShop($shop);
        }

        $this->entityManager->persist($newMenuElement);
        $this->entityManager->flush();

        return $newMenuElement;
    }

    private function persistMenuElementCategory(MenuElement $menuElement, $categoryId): MenuElementCategory
    {
        $newMenuElementCategory = new MenuElementCategory();
        $newMenuElementCategory->setMenuElement($menuElement);
        $newMenuElementCategory->setIdCategory($categoryId);
        $this->entityManager->persist($newMenuElementCategory);

        foreach ($this->languages as $language) {
            $newMenuElementCategoryLang = new MenuElementCategoryLang();

            $newMenuElementCategoryLang->setName($this->getCategoryNameByLangId($categoryId, $language['id_lang']));
            $newMenuElementCategoryLang->setLang($this->getLangEntityByIdLang($language['id_lang']));

            $newMenuElementCategory->addMenuElementCategoryLang($newMenuElementCategoryLang);
        }

        return $newMenuElementCategory;
    }

    private function getCategoryChildren($idCategory): array
    {
        return $this->categoryLegacyRepository->getCategoryChildren($idCategory);
    }

    private function getDefaultLanguageId(): int
    {
        if ($this->defaultLanguageId === null) {
            $this->defaultLanguageId = $this->configuration->getInt('PS_LANG_DEFAULT');
        }

        return $this->defaultLanguageId;
    }

    private function getLangEntityByIdLang(int $idLang): Lang
    {
        if (empty($this->langEntityCache[$idLang])) {
            $this->langEntityCache[$idLang] = $this->langRepository->findOneById($idLang);
        }

        return $this->langEntityCache[$idLang];
    }
}

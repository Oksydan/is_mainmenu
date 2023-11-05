<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\DataHandler;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementCategory;
use Oksydan\IsMainMenu\Entity\MenuElementCategoryLang;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;
use Oksydan\IsMainMenu\Repository\MenuElementCategoryRepository;
use PrestaShopBundle\Entity\Repository\LangRepository;

class MenuElementCategoryDataHandler implements RelatedEntitiesFormDataHandlerInterface
{
    /*
     * @var MenuElementCategoryRepository
     */
    private MenuElementCategoryRepository $menuElementCategoryRepository;

    /*
     * @var LangRepository
     */
    private LangRepository $langRepository;

    /*
     * @var array
     */
    private array $languages;

    public function __construct(
        MenuElementCategoryRepository $menuElementCategoryRepository,
        LangRepository $langRepository,
        array $languages
    ) {
        $this->menuElementCategoryRepository = $menuElementCategoryRepository;
        $this->langRepository = $langRepository;
        $this->languages = $languages;
    }

    public function handle(MenuElement $menuElement, array $data): MenuElementRelatedEntityInterface
    {
        $menuElementCategory = $this->menuElementCategoryRepository->findOneBy([
            'menuElement' => $menuElement->getId(),
        ]);

        if ($menuElementCategory instanceof MenuElementCategory) {
            $menuElementCategory->setIdCategory((int) $data['id_category'] ?? 0);

            foreach ($this->languages as $language) {
                $langId = (int) $language['id_lang'];
                $menuElementCategoryLang = $menuElementCategory->getMenuElementCategoryLangsByLangId($langId);

                if (null === $menuElementCategoryLang) {
                    continue;
                }

                $menuElementCategoryLang
                    ->setName($data['custom_name'][$langId] ?? '');
            }
        } else {
            $menuElementCategory = new MenuElementCategory();
            $menuElementCategory->setMenuElement($menuElement);
            $menuElementCategory->setIdCategory((int) $data['id_category'] ?? 0);

            foreach ($this->languages as $language) {
                $langId = (int) $language['id_lang'];
                $lang = $this->langRepository->findOneById($langId);
                $menuElementCategoryLang = new MenuElementCategoryLang();

                $menuElementCategoryLang
                    ->setLang($lang)
                    ->setName($data['custom_name'][$langId] ?? '');

                $menuElementCategory->addMenuElementCategoryLang($menuElementCategoryLang);
            }
        }

        return $menuElementCategory;
    }
}

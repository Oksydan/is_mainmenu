<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\DataHandler;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementCms;
use Oksydan\IsMainMenu\Entity\MenuElementCmsLang;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;
use Oksydan\IsMainMenu\Repository\MenuElementCmsRepository;
use PrestaShopBundle\Entity\Repository\LangRepository;

class MenuElementCmsDataHandler implements RelatedEntitiesFormDataHandlerInterface
{
    /*
     * @var EntityRepository
     */
    private MenuElementCmsRepository $menuElementCmsRepository;

    /*
     * @var LangRepository
     */
    private LangRepository $langRepository;

    /*
     * @var array
     */
    private array $languages;

    public function __construct(
        MenuElementCmsRepository $menuElementCmsRepository,
        LangRepository $langRepository,
        array $languages
    ) {
        $this->menuElementCmsRepository = $menuElementCmsRepository;
        $this->langRepository = $langRepository;
        $this->languages = $languages;
    }

    public function handle(MenuElement $menuElement, array $data): MenuElementRelatedEntityInterface
    {
        $menuElementCms = $this->menuElementCmsRepository->findOneBy([
            'menuElement' => $menuElement->getId(),
        ]);

        if ($menuElementCms instanceof MenuElementCms) {
            $menuElementCms->setIdCMS((int) $data['id_cms']);

            foreach ($this->languages as $language) {
                $langId = (int) $language['id_lang'];
                $menuElementCmsLang = $menuElementCms->getMenuElementCmsLangsByLangId($langId);

                if (null === $menuElementCmsLang) {
                    continue;
                }

                $menuElementCmsLang
                    ->setName($data['custom_name'][$langId] ?? '');
            }
        } else {
            $menuElementCms = new MenuElementCms();
            $menuElementCms->setMenuElement($menuElement);

            $menuElementCms->setIdCMS((int) $data['id_cms']);

            foreach ($this->languages as $language) {
                $langId = (int) $language['id_lang'];
                $lang = $this->langRepository->findOneById($langId);
                $menuElementCmsLang = new MenuElementCmsLang();

                $menuElementCmsLang
                    ->setLang($lang)
                    ->setName($data['custom_name'][$langId] ?? '');

                $menuElementCms->addMenuElementCmsLang($menuElementCmsLang);
            }
        }

        return $menuElementCms;
    }
}

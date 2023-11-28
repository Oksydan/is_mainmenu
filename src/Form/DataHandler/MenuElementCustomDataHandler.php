<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\DataHandler;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementCustom;
use Oksydan\IsMainMenu\Entity\MenuElementCustomLang;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;
use Oksydan\IsMainMenu\Repository\MenuElementCustomRepository;
use PrestaShopBundle\Entity\Repository\LangRepository;

class MenuElementCustomDataHandler implements RelatedEntitiesFormDataHandlerInterface
{
    /*
     * @var MenuElementCustomRepository
     */
    private MenuElementCustomRepository $menuElementCustomRepository;

    /*
     * @var LangRepository
     */
    private LangRepository $langRepository;

    /*
     * @var array
     */
    private array $languages;

    public function __construct(
        MenuElementCustomRepository $menuElementCustomRepository,
        LangRepository $langRepository,
        array $languages
    ) {
        $this->menuElementCustomRepository = $menuElementCustomRepository;
        $this->langRepository = $langRepository;
        $this->languages = $languages;
    }

    public function handle(MenuElement $menuElement, array $data): MenuElementRelatedEntityInterface
    {
        $menuElementCustom = $this->menuElementCustomRepository->findOneBy([
            'menuElement' => $menuElement->getId(),
        ]);

        if ($menuElementCustom instanceof MenuElementCustom) {
            foreach ($this->languages as $language) {
                $langId = (int) $language['id_lang'];
                $menuElementCustomLang = $menuElementCustom->getMenuElementCustomLangsByLangId($langId);

                if (null === $menuElementCustomLang) {
                    continue;
                }

                $menuElementCustomLang
                    ->setName($data['custom_name'][$langId] ?? '')
                    ->setUrl($data['url'][$langId] ?? '');
            }
        } else {
            $menuElementCustom = new MenuElementCustom();
            $menuElementCustom->setMenuElement($menuElement);

            foreach ($this->languages as $language) {
                $langId = (int) $language['id_lang'];
                $lang = $this->langRepository->findOneById($langId);
                $menuElementCustomLang = new MenuElementCustomLang();

                $menuElementCustomLang
                    ->setLang($lang)
                    ->setName($data['custom_name'][$langId] ?? '')
                    ->setUrl($data['url'][$langId] ?? '');

                $menuElementCustom->addMenuElementCustomLang($menuElementCustomLang);
            }
        }

        return $menuElementCustom;
    }
}

<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\DataHandler;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementHtml;
use Oksydan\IsMainMenu\Entity\MenuElementHtmlLang;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;
use Oksydan\IsMainMenu\Repository\MenuElementHtmlRepository;
use PrestaShopBundle\Entity\Repository\LangRepository;

class MenuElementHtmlDataHandler implements RelatedEntitiesFormDataHandlerInterface
{
    /*
     * @var EntityRepository
     */
    private MenuElementHtmlRepository $menuElementHtmlRepository;

    /*
     * @var LangRepository
     */
    private LangRepository $langRepository;

    /*
     * @var array
     */
    private array $languages;

    public function __construct(
        MenuElementHtmlRepository $menuElementHtmlRepository,
        LangRepository $langRepository,
        array $languages,
    ) {
        $this->menuElementHtmlRepository = $menuElementHtmlRepository;
        $this->langRepository = $langRepository;
        $this->languages = $languages;
    }

    public function handle(MenuElement $menuElement, array $data): MenuElementRelatedEntityInterface
    {
        $menuElementHtml = $this->menuElementHtmlRepository->findOneBy([
            'menuElement' => $menuElement->getId(),
        ]);

        if ($menuElementHtml instanceof MenuElementHtml) {
            foreach ($this->languages as $language) {
                $langId = (int) $language['id_lang'];
                $menuElementHtmlLang = $menuElementHtml->getMenuElementHtmlLangsByLangId($langId);

                if (null === $menuElementHtmlLang) {
                    continue;
                }

                $menuElementHtmlLang
                    ->setName($data['custom_name'][$langId] ?? '')
                    ->setContent($data['content'][$langId] ?? '');
            }
        } else {
            $menuElementHtml = new MenuElementHtml();
            $menuElementHtml->setMenuElement($menuElement);

            foreach ($this->languages as $language) {
                $langId = (int) $language['id_lang'];
                $lang = $this->langRepository->findOneById($langId);
                $menuElementHtmlLang = new MenuElementHtmlLang();

                $menuElementHtmlLang
                    ->setLang($lang)
                    ->setName($data['custom_name'][$langId] ?? '')
                    ->setContent($data['content'][$langId] ?? '');

                $menuElementHtml->addMenuElementHtmlLang($menuElementHtmlLang);
            }
        }

        return $menuElementHtml;
    }
}

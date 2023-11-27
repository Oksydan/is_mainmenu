<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\ChoiceProvider;

use Oksydan\IsMainMenu\Menu\MenuLayoutGrid;
use Oksydan\IsMainMenu\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuLayoutGridChoiceProvider implements FormChoiceProviderInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getChoices(): array
    {
        $choices = [];

        foreach (MenuLayoutGrid::KEYS as $type) {
            $choices[$this->getLabelForChoice($type)] = $type;
        }

        return $choices;
    }

    private function getLabelForChoice(string $type): string
    {
        switch ($type) {
            case MenuLayoutGrid::GRID_AUTO:
                $label = $this->translator->trans('Column auto', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_FILL:
                $label = $this->translator->trans('Column fill', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_1:
                $label = $this->translator->trans('Column width 8.3% (1/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_2:
                $label = $this->translator->trans('Column width 16.6% (2/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_3:
                $label = $this->translator->trans('Column width 25% (3/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_4:
                $label = $this->translator->trans('Column width 33.3% (4/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_5:
                $label = $this->translator->trans('Column width 41.6% (5/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_6:
                $label = $this->translator->trans('Column width 50% (6/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_7:
                $label = $this->translator->trans('Column width 58.3% (7/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_8:
                $label = $this->translator->trans('Column width 66.6% (8/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_9:
                $label = $this->translator->trans('Column width 75% (9/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_10:
                $label = $this->translator->trans('Column width 83.3% (10/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_11:
                $label = $this->translator->trans('Column width 91.6% (11/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuLayoutGrid::GRID_12:
                $label = $this->translator->trans('Column width 100% (12/12)', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            default:
                throw new \Exception('Unknown type: ' . $type . ' for grid type');
        }

        return $label;
    }
}

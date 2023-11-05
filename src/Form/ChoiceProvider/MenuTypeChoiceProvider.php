<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\ChoiceProvider;

use Oksydan\IsMainMenu\Entity\MenuELement;
use Oksydan\IsMainMenu\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuTypeChoiceProvider implements FormChoiceProviderInterface
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

        foreach (MenuElement::TYPE_CHOICES as $type) {
            $choices[$this->getLabelForChoice($type)] = $type;
        }

        return $choices;
    }

    private function getLabelForChoice(string $type): string
    {
        switch ($type) {
            case MenuElement::TYPE_CATEGORY:
                $label = $this->translator->trans('Category', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuElement::TYPE_LINK:
                $label = $this->translator->trans('Custom link', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuElement::TYPE_BANNER:
                $label = $this->translator->trans('Banner', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuElement::TYPE_HTML:
                $label = $this->translator->trans('HTML', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            case MenuElement::TYPE_CMS:
                $label = $this->translator->trans('CMS', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
                break;
            default:
                throw new \Exception('Unknown type: ' . $type . ' for menu element');
        }

        return $label;
    }
}

<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

class CMSPagesChoiceProvider implements FormChoiceProviderInterface
{
    private \Context $context;

    public function __construct(\Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getChoices(): array
    {
        $choices = [];

        foreach ($this->getCMSPages() as $cms) {
            $choices[$cms['meta_title']] = $cms['id_cms'];
        }

        return $choices;
    }

    private function getCMSPages(): array
    {
        return \CMS::getCMSPages($this->context->language->id);
    }
}

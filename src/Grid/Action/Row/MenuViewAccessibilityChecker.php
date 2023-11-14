<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Grid\Action\Row;

use Oksydan\IsMainMenu\Entity\MenuElement;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AccessibilityCheckerInterface;

/**
 * Checks if you should be able to access deeper menu element.
 */
class MenuViewAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isGranted(array $record)
    {
        return in_array($record['type'], [
            MenuElement::TYPE_CATEGORY,
            MenuElement::TYPE_CMS,
            MenuElement::TYPE_LINK,
        ]);
    }
}

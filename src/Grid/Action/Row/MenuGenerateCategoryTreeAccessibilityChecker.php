<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Grid\Action\Row;

use Oksydan\IsMainMenu\Entity\MenuElement;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AccessibilityCheckerInterface;

/**
 * Checks if you should be able to generate category tree for menu element.
 */
class MenuGenerateCategoryTreeAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isGranted(array $record)
    {
        return in_array($record['type'], [
            MenuElement::TYPE_CATEGORY,
        ]);
    }
}

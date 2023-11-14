<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Grid\Definition\Factory;

use Oksydan\IsMainMenu\Grid\Action\Row\MenuGenerateCategoryTreeAccessibilityChecker;
use Oksydan\IsMainMenu\Grid\Action\Row\MenuViewAccessibilityChecker;
use Oksydan\IsMainMenu\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuListGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $trans;

    /**
     * @var MenuViewAccessibilityChecker
     */
    private MenuViewAccessibilityChecker $menuViewAccessibilityChecker;

    /**
     * @var MenuGenerateCategoryTreeAccessibilityChecker
     */
    private MenuGenerateCategoryTreeAccessibilityChecker $menuGenerateCategoryTreeAccessibilityChecker;

    public function __construct(
        HookDispatcherInterface $hookDispatcher = null,
        TranslatorInterface $trans,
        MenuViewAccessibilityChecker $menuViewAccessibilityChecker,
        MenuGenerateCategoryTreeAccessibilityChecker $menuGenerateCategoryTreeAccessibilityChecker
    ) {
        parent::__construct($hookDispatcher);
        $this->trans = $trans;
        $this->menuViewAccessibilityChecker = $menuViewAccessibilityChecker;
        $this->menuGenerateCategoryTreeAccessibilityChecker = $menuGenerateCategoryTreeAccessibilityChecker;
    }

    public const GRID_ID = 'is_mainmenu_list';

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans->trans('Main menu list', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new PositionColumn('position'))
                    ->setName($this->trans->trans('Position', [], 'Admin.Global'))
                    ->setOptions([
                        'id_field' => 'id_menu_element',
                        'position_field' => 'position',
                        'update_route' => 'is_mainmenu_controller_update_position',
                        'update_method' => 'POST',
                    ])
            )
            ->add(
                (new DataColumn('id_menu_element'))
                    ->setName($this->trans->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_menu_element',
                    ])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans->trans('Title', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add(
                (new DataColumn('type'))
                    ->setName($this->trans->trans('Menu type', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN))
                    ->setOptions([
                        'field' => 'type',
                    ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans->trans('Active', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_menu_element',
                        'route' => 'is_mainmenu_controller_toggle_active',
                        'route_param_name' => 'menuItemId',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('view'))
                                    ->setIcon('zoom_in')
                                    ->setOptions([
                                        'accessibility_checker' => $this->menuViewAccessibilityChecker,
                                        'route' => 'is_mainmenu_controller_list',
                                        'route_param_name' => 'menuItemId',
                                        'route_param_field' => 'id_menu_element',
                                    ])
                            )
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setName($this->trans->trans('Edit', [], 'Admin.Actions'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'is_mainmenu_controller_edit',
                                        'route_param_name' => 'menuItemId',
                                        'route_param_field' => 'id_menu_element',
                                    ])
                            )
                            ->add(
                                (new LinkRowAction('generate_tree'))
                                    ->setName($this->trans->trans('Generate category tree', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN))
                                    ->setIcon('playlist_add')
                                    ->setOptions([
                                        'accessibility_checker' => $this->menuGenerateCategoryTreeAccessibilityChecker,
                                        'route' => 'is_mainmenu_controller_generate_category_tree',
                                        'route_param_name' => 'menuItemId',
                                        'route_param_field' => 'id_menu_element',
                                        'confirm_message' => $this->trans->trans(
                                            'Are you sure you want to generate category tree for this menu item? This action will remove all existing menu items for this element.',
                                            [],
                                            TranslationDomains::TRANSLATION_DOMAIN_ADMIN
                                        ),
                                    ])
                            )
                            ->add(
                                (new LinkRowAction('delete'))
                                    ->setName($this->trans->trans('Delete', [], 'Admin.Actions'))
                                    ->setIcon('delete')
                                    ->setOptions([
                                        'route' => 'is_mainmenu_controller_delete',
                                        'route_param_name' => 'menuItemId',
                                        'route_param_field' => 'id_menu_element',
                                        'confirm_message' => $this->trans->trans(
                                            'Delete selected item?',
                                            [],
                                            'Admin.Notifications.Warning'
                                        ),
                                    ])
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return new FilterCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return new GridActionCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return new BulkActionCollection();
    }
}

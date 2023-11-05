<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Controller;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Filter\MenuListFilters;
use Oksydan\IsMainMenu\Form\DataHandler\MenuElementFormDataHandler;
use Oksydan\IsMainMenu\Form\Provider\MenuElementFormDataProvider;
use Oksydan\IsMainMenu\Form\Type\MenuElementType;
use Oksydan\IsMainMenu\Handler\MenuElement\DeleteMenuElementHandler;
use Oksydan\IsMainMenu\Handler\MenuElement\ToggleMenuElementStatusHandler;
use Oksydan\IsMainMenu\Handler\MenuElement\UpdateMenuElementPositionHandler;
use Oksydan\IsMainMenu\Provider\MenuListBreadcrumbDataProvider;
use Oksydan\IsMainMenu\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMenuController extends FrameworkBundleAdminController
{
    public function indexAction(MenuListFilters $filters): Response
    {
        // WORST PRACTICE EVER BUT WE CAN'T ADD IT UPON INSTALLATION
        $this->installRootElementIfNotExists();
        $root = $this->getRootElement();

        $filters->addFilter(['id_parent_menu_element' => $root->getId()]);

        $addNewLink = $this->generateUrl('is_mainmenu_controller_add');
        $menuGridFactory = $this->get('oksydan.is_mainmenu.grid.menu_list_grid_factory');

        $menuGrid = $menuGridFactory->getGrid($filters);

        return $this->render('@Modules/is_mainmenu/views/templates/admin/index.html.twig', [
            'translationsDomain' => TranslationDomains::TRANSLATION_DOMAIN_ADMIN,
            'menuGrid' => $this->presentGrid($menuGrid),
            'newMenuElementUrl' => $addNewLink,
            'listTitle' => $this->getListTitle(),
        ]);
    }

    public function listAction(
        Request $request,
        MenuListFilters $filters,
        int $menuItemId
    ): Response
    {
        $addNewLink = $this->generateUrl('is_mainmenu_controller_add', ['menuItemId' => $menuItemId]);
        $menuGridFactory = $this->get('oksydan.is_mainmenu.grid.menu_list_grid_factory');

        if ($menuItemId === $this->getRootElement()->getId()) {
            return $this->redirectToRoute('is_mainmenu_controller_index');
        }

        $redirectResponse = $this->controlElementChildrenPermissions($menuItemId);

        if ($redirectResponse instanceof RedirectResponse) {
            return $redirectResponse;
        }

        $filters->addFilter(['id_parent_menu_element' => $menuItemId]);
        $menuElement = $this->get('oksydan.is_mainmenu.menu_element_repository')->find($menuItemId);
        $menuListBreadcrumbDataProvider = $this->get(MenuListBreadcrumbDataProvider::class);

        $menuGrid = $menuGridFactory->getGrid($filters);

        return $this->render('@Modules/is_mainmenu/views/templates/admin/index.html.twig', [
            'translationsDomain' => TranslationDomains::TRANSLATION_DOMAIN_ADMIN,
            'menuGrid' => $this->presentGrid($menuGrid),
            'newMenuElementUrl' => $addNewLink,
            'backToParentUrl' => $this->getBackToParentElementUrl((int) $menuItemId),
            'currentMenuElementId' => $menuItemId,
            'listTitle' => $this->getListTitle((int) $menuItemId),
            'breadcrumb' => $menuListBreadcrumbDataProvider->provide($menuElement),
        ]);
    }

    private function getListTitle(int $menuItemId = 0): string
    {
        if ($menuItemId > 0) {
            $repository = $this->get('oksydan.is_mainmenu.menu_element_repository');
            $menuElement = $repository->find($menuItemId);

            if ($menuElement instanceof MenuElement) {
                return sprintf($this->trans('Menu list for: %s', TranslationDomains::TRANSLATION_DOMAIN_ADMIN), $menuElement->getName());
            }
        }

        return $this->trans('Menu list', TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
    }

    private function getBackToParentElementUrl(int $menuItemId): string
    {
        $repository = $this->get('oksydan.is_mainmenu.menu_element_repository');
        $menuElement = $repository->find($menuItemId);

        if ($menuElement instanceof MenuElement) {
            $parentElement = $menuElement->getParentMenuElement();

            if ($parentElement instanceof MenuElement && $parentElement->getId() !== $this->getRootElement()->getId()) {
                return $this->generateUrl('is_mainmenu_controller_list', ['menuItemId' => $parentElement->getId()]);
            }
        }

        return $this->generateUrl('is_mainmenu_controller_index');
    }

    private function getForm(int $parentId = 0)
    {
        $formBuilder = $this->get('prestashop.core.form.builder.form_builder_factory')->create(
            MenuElementType::class,
            $this->get(MenuElementFormDataProvider::class)
        );

        return $formBuilder->getForm(['id_parent_element' => $parentId]);
    }

    private function getFormFor(int $idMenuElement)
    {
        $formBuilder = $this->get('prestashop.core.form.builder.form_builder_factory')->create(
            MenuElementType::class,
            $this->get(MenuElementFormDataProvider::class)
        );

        return $formBuilder->getFormFor($idMenuElement);
    }

    private function getFormHandler()
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.form_handler_factory')->create(
            $this->get(MenuElementFormDataHandler::class)
        );
    }

    public function addAction(Request $request): Response
    {
        $parentId = (int) $request->query->get('menuItemId', $this->getRootElement()->getId());
        $form = $this->getForm($parentId);
        $form->handleRequest($request);
        $formHandler = $this->getFormHandler();

        $redirectResponse = $this->controlElementChildrenPermissions($parentId);

        if ($redirectResponse instanceof RedirectResponse) {
            return $redirectResponse;
        }

        try {
            $result = $formHandler->handle($form);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful creation.', 'Admin.Notifications.Success')
                );

                return $this->redirectToRoute('is_mainmenu_controller_edit', ['menuItemId' => $result->getIdentifiableObjectId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('@Modules/is_mainmenu/views/templates/admin/form.html.twig', [
            'form' => $form->createView(),
            'title' => $this->trans('Add new menu item', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
            'help_link' => false,
            'back_btn_link' => $this->generateUrl('is_mainmenu_controller_list', ['menuItemId' => $parentId]),
        ]);
    }

    public function editAction(Request $request, int $menuItemId): Response
    {
        $form = $this->getFormFor($menuItemId);
        $form->handleRequest($request);
        $formHandler = $this->getFormHandler();
        $repository = $this->get('oksydan.is_mainmenu.menu_element_repository');
        $menuElement = $repository->find($menuItemId);
        $parentElement = $menuElement->getParentMenuElement();
        $parentId = $parentElement instanceof MenuElement ? $parentElement->getId() : $this->getRootElement()->getId();

        try {
            $result = $formHandler->handleFor($menuItemId, $form);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful edited.', 'Admin.Notifications.Success')
                );

                $repository = $this->get('oksydan.is_mainmenu.menu_element_repository');
                $menuElement = $repository->find($menuItemId);

                if ($menuElement instanceof MenuElement) {
                    return $this->redirectToMenuList($parentId);
                } else {
                    return $this->redirectToRoute('is_mainmenu_controller_index');
                }
            }
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('@Modules/is_mainmenu/views/templates/admin/form.html.twig', [
            'form' => $form->createView(),
            'title' => $this->trans('Edit menu item', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
            'help_link' => false,
            'back_btn_link' => $this->generateUrl('is_mainmenu_controller_list', ['menuItemId' => $parentId]),
        ]);
    }

    private function controlElementChildrenPermissions($menuElementId)
    {
        $repository = $this->get('oksydan.is_mainmenu.menu_element_repository');
        $menuElement = $repository->find($menuElementId);

        if ($menuElement instanceof MenuElement) {
            if (in_array($menuElement->getType(), [MenuElement::TYPE_BANNER, MenuELement::TYPE_HTML])) {
                $this->addFlash(
                    'error',
                    $this->trans(
                        'You can\'t add children to element with type of banner or html content',
                        TranslationDomains::TRANSLATION_DOMAIN_ADMIN
                    )
                );

                return $this->redirectToRoute('is_mainmenu_controller_list', [
                    'menuItemId' => $menuElement->getParentMenuElement()->getId(),
                ]);
            }
        } else {
            return $this->redirectToRoute('is_mainmenu_controller_index');
        }
    }

    private function redirectToMenuList($parentId = null)
    {
        if ($this->getRootElement()->getId() !== $parentId) {
            return $this->redirectToRoute('is_mainmenu_controller_list', ['menuItemId' => $parentId]);
        } else {
            return $this->redirectToRoute('is_mainmenu_controller_index');
        }
    }

    public function deleteAction(Request $request, int $menuItemId): Response
    {
        $repository = $this->get('oksydan.is_mainmenu.menu_element_repository');
        $menuElement = $repository->find($menuItemId);
        $parentElement = $menuElement->getParentMenuElement();
        $parentId = $parentElement instanceof MenuElement ? $parentElement->getId() : $this->getRootElement()->getId();

        if ($menuElement instanceof MenuElement) {
            try {
                $handler = $this->get(DeleteMenuElementHandler::class);
                $handler->handle($menuItemId);

                $this->addFlash('success', $this->trans('Menu element removed successfully', TranslationDomains::TRANSLATION_DOMAIN_ADMIN));

                return $this->redirectToMenuList($parentId);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());

                return $this->redirectToMenuList($parentId);
            }
        }

        $this->addFlash('error', $this->trans('Menu element don\'t exists', TranslationDomains::TRANSLATION_DOMAIN_ADMIN));

        return $this->redirectToRoute('is_mainmenu_controller_index');
    }

    /**
     * @param Request $request
     * @param int $menuItemId
     *
     * @return Response
     */
    public function toggleActiveAction(Request $request, int $menuItemId): Response
    {
        try {
            $handler = $this->get(ToggleMenuElementStatusHandler::class);
            $handler->handle($menuItemId);

            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'),
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }

        return $this->json($response);
    }

    public function updatePositionAction(Request $request): Response
    {
        try {
            $handler = $this->get(UpdateMenuElementPositionHandler::class);
            $parentId = $handler->handle($request->request->get('positions'));

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

            return $this->redirectToMenuList($parentId);
        } catch (PositionDataException|PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('is_mainmenu_controller_index');
    }

    private function installRootElementIfNotExists(): bool
    {
        if (!($this->getRootElement() instanceof MenuElement)) {
            $menuElement = new MenuElement();
            $menuElement->setActive(true);
            $menuElement->setDepth(0);
            $menuElement->setIsRoot(true);
            $menuElement->setName('Main menu');
            $menuElement->setPosition(0);
            $menuElement->setCssClass('');
            $menuElement->setType(MenuElement::TYPE_ROOT);

            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($menuElement);
                $em->flush();

                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    private function getRootElement(): ?MenuElement
    {
        $repository = $this->get('oksydan.is_mainmenu.menu_element_repository');
        $menuElement = $repository->getRootElement();

        if ($menuElement instanceof MenuElement) {
            return $menuElement;
        } else {
            return null;
        }
    }
}

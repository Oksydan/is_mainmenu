<?php

use Oksydan\IsMainMenu\Repository\MenuElementRepository;
use Oksydan\IsMainMenu\View\Front\DesktopSubMenuRender;
use Oksydan\IsMainMenu\View\Front\MobileSubMenuRender;

class Is_mainmenuAjaxModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->ajax = true;

        parent::init();

        $action = Tools::getValue('action', '');

        switch ($action) {
            case 'getDesktopSubMenu':
                $this->getDesktopSubMenu();
                break;
            case 'getMobileSubMenu':
                $this->getMobileSubMenu();
                break;
            default:
                $this->renderResponse([
                    'success' => false,
                    'message' => 'Action ' . $action . ' does not exist',
                ]);
        }
    }

    public function getDesktopSubMenu()
    {
        $idMenuElement = (int) Tools::getValue('id_menu_element', 0);

        try {
            $this->checkMenuElementExistence($idMenuElement);
        } catch (Exception $e) {
            $this->renderResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        try {
            $menuRender = $this->get(DesktopSubMenuRender::class);
        } catch (Exception $e) {
            $this->renderResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        $this->renderResponse([
            'success' => true,
            'html' => $menuRender->render($idMenuElement),
        ]);
    }

    public function getMobileSubMenu()
    {
        $idMenuElements = Tools::getValue('id_menu_elements', []);

        try {
            foreach ($idMenuElements as $idMenuElement) {
                $this->checkMenuElementExistence($idMenuElement);
            }
        } catch (Exception $e) {
            $this->renderResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        try {
            $menuRender = $this->get(MobileSubMenuRender::class);
        } catch (Exception $e) {
            $this->renderResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        $html = '';

        foreach ($idMenuElements as $idMenuElement) {
            $html .= $menuRender->render($idMenuElement);
        }

        $this->renderResponse([
            'success' => true,
            'html' => $html,
        ]);
    }

    public function checkMenuElementExistence(int $idMenuElement)
    {
        $repository = $this->get(MenuElementRepository::class);

        $exist = (bool) $repository->find($idMenuElement);

        if (!$exist) {
            throw new Exception('Menu element with id ' . $idMenuElement . ' does not exist');
        }
    }

    protected function renderResponse($data): void
    {
        ob_end_clean();
        header('Content-Type: application/json');
        header('Cache-Control: max-age=604800, stale-while-revalidate=86400');

        echo json_encode($data);

        exit;
    }
}

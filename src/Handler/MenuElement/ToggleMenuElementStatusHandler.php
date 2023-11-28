<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Handler\MenuElement;

use Doctrine\ORM\EntityManagerInterface;
use Oksydan\IsMainMenu\Cache\ModuleCache;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Translations\TranslationDomains;
use Symfony\Contracts\Translation\TranslatorInterface;

class ToggleMenuElementStatusHandler implements MenuElementHandlerInterface
{
    private EntityManagerInterface $entityManager;

    private TranslatorInterface $translator;

    private ModuleCache $moduleCache;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ModuleCache $moduleCache
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->moduleCache = $moduleCache;
    }

    public function handle(int $idMenuElement): void
    {
        $menuElement = $this->entityManager
            ->getRepository(MenuElement::class)
            ->findOneBy(['id' => $idMenuElement]);

        if (empty($menuElement)) {
            throw new \Exception(sprintf($this->translator->trans('Menu element with id %d not found', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN), $idMenuElement));
        }

        $menuElement->setActive(!$menuElement->getActive());

        try {
            $this->entityManager->flush();

            $this->moduleCache->clearCache();
        } catch (\Exception $e) {
            throw new \Exception(sprintf($this->translator->trans('There was an error while updating the status of element %d', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN), $idMenuElement));
        }
    }
}

<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Handler\MenuElement;

use Doctrine\ORM\EntityManagerInterface;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Translations\TranslationDomains;
use Symfony\Contracts\Translation\TranslatorInterface;

class ToggleMenuElementStatusHandler
{
    private EntityManagerInterface $entityManager;

    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
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
        } catch (\Exception $e) {
            throw new \Exception(sprintf($this->translator->trans('There was an error while updating the status of element %d', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN), $idMenuElement));
        }
    }
}

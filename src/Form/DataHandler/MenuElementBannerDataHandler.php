<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\DataHandler;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementBanner;
use Oksydan\IsMainMenu\Entity\MenuElementBannerLang;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;
use Oksydan\IsMainMenu\Factory\FileEraserFactory;
use Oksydan\IsMainMenu\Factory\FileUploaderFactory;
use Oksydan\IsMainMenu\Repository\MenuElementBannerRepository;
use PrestaShopBundle\Entity\Repository\LangRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MenuElementBannerDataHandler implements RelatedEntitiesFormDataHandlerInterface
{
    /*
     * @var MenuElementBannerRepository
     */
    private MenuElementBannerRepository $menuElementBannerRepository;

    /*
     * @var LangRepository
     */
    private LangRepository $langRepository;

    /*
     * @var array
     */
    private array $languages;

    /*
     * @var FileUploaderFactory
     */
    private FileUploaderFactory $fileUploaderFactory;

    /*
     * @var FileEraserFactory
     */
    private FileEraserFactory $fileEraserFactory;

    public function __construct(
        MenuElementBannerRepository $menuElementBannerRepository,
        LangRepository $langRepository,
        array $languages,
        FileUploaderFactory $fileUploaderFactory,
        FileEraserFactory $fileEraserFactory
    ) {
        $this->menuElementBannerRepository = $menuElementBannerRepository;
        $this->langRepository = $langRepository;
        $this->languages = $languages;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->fileEraserFactory = $fileEraserFactory;
    }

    public function handle(MenuElement $menuElement, array $data): MenuElementRelatedEntityInterface
    {
        $menuElementBanner = $this->menuElementBannerRepository->findOneBy([
            'menuElement' => $menuElement->getId(),
        ]);

        if ($menuElementBanner instanceof MenuElementBanner) {
            foreach ($this->languages as $language) {
                $langId = (int) $language['id_lang'];
                $menuElementBannerLang = $menuElementBanner->getMenuElementBannerLangsByLangId($langId);

                if (null === $menuElementBannerLang) {
                    continue;
                }

                if (isset($data['image'][$langId]) && $data['image'][$langId] instanceof UploadedFile) {
                    if ($menuElementBannerLang->getFilename()) {
                        $this->eraseFile($menuElementBannerLang->getFilename());
                    }

                    $fileName = $this->uploadFile($data['image'][$langId]);
                    $menuElementBannerLang->setFilename($fileName);
                }

                $menuElementBannerLang
                    ->setName($data['custom_name'][$langId] ?? '')
                    ->setUrl($data['url'][$langId] ?? '')
                    ->setAlt($data['alt'][$langId] ?? '');
            }
        } else {
            $menuElementBanner = new MenuElementBanner();
            $menuElementBanner->setMenuElement($menuElement);

            foreach ($this->languages as $language) {
                $langId = (int) $language['id_lang'];
                $lang = $this->langRepository->findOneById($langId);
                $menuElementBannerLang = new MenuElementBannerLang();

                if (isset($data['image'][$langId]) && $data['image'][$langId] instanceof UploadedFile) {
                    $fileName = $this->uploadFile($data['image'][$langId]);
                    $menuElementBannerLang->setFilename($fileName);
                }

                $menuElementBannerLang
                    ->setLang($lang)
                    ->setName($data['custom_name'][$langId] ?? '')
                    ->setUrl($data['url'][$langId] ?? '')
                    ->setAlt($data['alt'][$langId] ?? '');

                $menuElementBanner->addMenuElementBannerLang($menuElementBannerLang);
            }
        }

        return $menuElementBanner;
    }

    private function uploadFile(UploadedFile $file): string
    {
        $fileUploader = $this->fileUploaderFactory->create(FileUploaderFactory::IMAGE_DIR);

        return $fileUploader->upload($file);
    }

    private function eraseFile(string $fileName): bool
    {
        $fileEraser = $this->fileEraserFactory->create(FileEraserFactory::IMAGE_DIR);

        return $fileEraser->remove($fileName);
    }
}

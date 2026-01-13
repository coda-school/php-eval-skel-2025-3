<?php

namespace App\Service;


use App\Entity\UserSettings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class UserSettingsService
{
    public function __construct(
        private EntityManagerInterface                                            $entityManager,
        private SluggerInterface                                                  $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/avatars')] private string $avatarsDirectory)
    {
    }

    public function handleSave(UserSettings $settings, ?UploadedFile $avatarFile): void
    {
        if ($avatarFile) {
            $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

            try {
                $avatarFile->move($this->avatarsDirectory, $newFilename);
                $settings->getOwner()->setAvatar('uploads/avatars/' . $newFilename);
            } catch (FileException $e) {
                echo $e->getMessage();
            }
        }
        $this->entityManager->persist($settings);
        $this->entityManager->flush();
    }

}

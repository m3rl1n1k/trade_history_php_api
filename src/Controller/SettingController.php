<?php

namespace App\Controller;

use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/setting')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class SettingController extends BaseController
{
    #[Route('/', name: 'app_settings', methods: ['GET'])]
    public function index(SettingRepository $settingRepository): JsonResponse
    {
        if (null !== $permission = $this->checkUserAccess()) {
            return $permission;
        }

        $user = $this->getUser()->getId();
        $setting = $settingRepository->findOneBy(['user' => $this->getUser()]);

        if ($setting === null) {
            $settingRepository->save($setting, $user);
            $setting = [
                'message' => "Reload page after successful generate settings",
            ];
        }
        return $this->jsonResponse(['setting' => $setting], context: [
            AbstractNormalizer::ATTRIBUTES => [
                'setting',
                'user' => []
            ]
        ]);
    }

    #[Route('/edit', name: 'app_setting_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, SettingRepository $settingRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $setting = $settingRepository->findOneBy(['user' => $this->getUser()]);

        if (null !== $permission = $this->checkUserAccess($setting)) {
            return $permission;
        }

        $originalSetting = $this->decodeJson($setting->getSetting());
        $updatedSetting = $this->decodeJson($request->getContent())['setting'];

        $toSave = array_replace_recursive($originalSetting, $updatedSetting);

        $setting->setSetting($toSave);
        $entityManager->flush();

        return $this->jsonResponse(['message' => "Setting updated successfully"]);
    }


}
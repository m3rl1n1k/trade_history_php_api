<?php

namespace App\Controller;

use App\Repository\SettingRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $this->checkPermission();

        $user = $this->getUser()->getId();
        $settings = $settingRepository->findOneBy(['user' => $this->getUser()]);

        if ($settings === null) {
            $settingRepository->save($settings, $user);
            $settings = [
                'message' => "Reload page after successful generate settings",
            ];
        }
        return $this->jsonResponse($settings, context: [
            AbstractNormalizer::ATTRIBUTES => [
                'setting',
                'user' => []
            ]
        ]);
    }


}
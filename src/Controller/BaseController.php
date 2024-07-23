<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class BaseController extends AbstractController
{

    protected function jsonResponse($data, $statusCode = Response::HTTP_OK, $headers = [], array $context = []): JsonResponse
    {
        return $this->json($data, $statusCode, $headers, array_merge($context, [
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
            'json_encode_options' => JSON_UNESCAPED_SLASHES
        ]));
    }

    public function permission(object $class): ?JsonResponse
    {
        if ($this->getUser()->getUserIdentifier() === null) {
            $this->redirectToRoute('app_index');
        }
        if ($this->getUser()->getId() !== $class->getUser()->getId()) {
            return $this->json("You don't have access to this resource.", Response::HTTP_FORBIDDEN);
        }
        return null;
    }

    protected function checkPermission(object $class = null): ?JsonResponse
    {
        $permission = $this->permission($class);
        if ($permission !== null) {
            return $permission;
        }
        return null;
    }

    protected function decodeJson(string $string)
    {
        return json_decode($string, true);
    }


}
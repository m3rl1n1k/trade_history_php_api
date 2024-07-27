<?php

namespace App\Controller;

use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class BaseController extends AbstractController
{
    protected function decodeJson(string $string): stdClass
    {
        return json_decode($string);
    }

    protected function checkUserAccess(?object $class = null): ?JsonResponse
    {
        if ($this->getUser()->getUserIdentifier() === null) {
            $this->redirectToRoute('app_index');
        }
        if ($class !== null && $this->getUser()->getId() !== $class->getUser()->getId()) {
            return $this->jsonResponse(["message" => "You don't have access to this resource."], Response::HTTP_FORBIDDEN);
        }
        return null;
    }

    protected function jsonResponse($data, $statusCode = Response::HTTP_OK, $headers = [], array $context = []): JsonResponse
    {
        return $this->json($data, $statusCode, $headers, array_merge($context, [
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
            'json_encode_options' => JSON_UNESCAPED_SLASHES
        ]));
    }

    protected function notFoundItemsResponse($items): JsonResponse|false
    {
        foreach ($items as $item) {
            if (is_string($item)) {
                return $this->jsonResponse(["message" => "Not found element", 'item' => $item], Response::HTTP_NOT_FOUND);
            }
        }
        return false;
    }


}
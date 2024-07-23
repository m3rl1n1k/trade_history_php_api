<?php

namespace App\Serializer\Normalizer;

use App\Entity\MainCategory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MainCategoryNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer,
        protected RouterInterface   $router
    )
    {
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);
        if ($object->getId())
            $data['url'] = $this->router->generate('app_main_category_show', ['id' => $object->getId()]);

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof MainCategory;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [MainCategory::class => true];
    }
}

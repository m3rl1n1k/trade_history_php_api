<?php

namespace App\Serializer\Normalizer;

use App\Entity\Wallet;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class WalletNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer,
        protected RouterInterface            $router,
    )
    {
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Wallet::class => true];
    }

    public function normalize($object, ?string $format = null, array $context = []): array|string
    {
        $data = $this->normalizer->normalize($object, $format, $context);
        if ($object->getId())
            $data['url'] = $this->router->generate('app_wallet_show', ['id' => $object->getId()]);

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Wallet;
    }
}

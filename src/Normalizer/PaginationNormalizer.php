<?php

namespace App\Normalizer;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class PaginationNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')] private NormalizerInterface $normalizer
    ) {
    }

    #[\Override]
    public function normalize(
        mixed $object,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|\ArrayObject|null {
        if (!($object instanceof PaginationInterface)) {
            throw new \InvalidArgumentException('The object must be an instance of PaginationInterface');
        }

        return [
            'items' => array_map(
                fn(object $item) => $this->normalizer->normalize($item, $format, $context),
                $object->getItems()
            ),
            'pagination' => [
                'totalItems' => $object->getTotalItemCount(),
                'itemsPerPage' => $object->getItemNumberPerPage(),
                'totalPages' => ceil($object->getTotalItemCount() / $object->getItemNumberPerPage()),
                'currentPage' => $object->getCurrentPageNumber(),
            ],
        ];
    }

    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof PaginationInterface && $format === 'json';
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            PaginationInterface::class => true,
        ];
    }
}

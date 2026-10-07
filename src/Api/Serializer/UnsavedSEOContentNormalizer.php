<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Api\Serializer;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

/**
 * ReferenceableTrait lazily creates an empty SEOContent when none is stored yet.
 * Such a content has no identifier, so API Platform cannot generate its IRI:
 * expose it as null instead.
 */
final class UnsavedSEOContentNormalizer implements ContextAwareNormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        return null;
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof SEOContentInterface && null === $data->getId();
    }
}

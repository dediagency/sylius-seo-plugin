<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Resolver;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestedProductVariantResolver implements ProductVariantResolverInterface
{
    public const QUERY_PARAMETER = 'variant';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function getVariant(ProductInterface $subject): ?ProductVariantInterface
    {
        $code = $this->requestStack->getMainRequest()?->query->get(self::QUERY_PARAMETER);

        if (!is_string($code) || '' === $code) {
            return null;
        }

        foreach ($subject->getEnabledVariants() as $variant) {
            if ($variant->getCode() === $code) {
                return $variant;
            }
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace spec\Dedi\SyliusSEOPlugin\RichSnippet\Resolver;

use Dedi\SyliusSEOPlugin\RichSnippet\Resolver\RequestedProductVariantResolver;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestedProductVariantResolverSpec extends ObjectBehavior
{
    function let(RequestStack $requestStack)
    {
        $this->beConstructedWith($requestStack);
    }

    function it_is_a_product_variant_resolver()
    {
        $this->shouldHaveType(RequestedProductVariantResolver::class);
        $this->shouldImplement(ProductVariantResolverInterface::class);
    }

    function it_returns_the_enabled_variant_given_in_the_query(
        RequestStack $requestStack,
        ProductInterface $product,
        ProductVariantInterface $small,
        ProductVariantInterface $large,
    ) {
        $requestStack->getMainRequest()->willReturn(new Request(['variant' => 'LARGE']));
        $small->getCode()->willReturn('SMALL');
        $large->getCode()->willReturn('LARGE');
        $product->getEnabledVariants()->willReturn(new ArrayCollection([$small->getWrappedObject(), $large->getWrappedObject()]));

        $this->getVariant($product)->shouldReturn($large);
    }

    function it_returns_null_when_the_variant_is_not_an_enabled_variant_of_the_product(
        RequestStack $requestStack,
        ProductInterface $product,
        ProductVariantInterface $small,
    ) {
        $requestStack->getMainRequest()->willReturn(new Request(['variant' => 'OTHER']));
        $small->getCode()->willReturn('SMALL');
        $product->getEnabledVariants()->willReturn(new ArrayCollection([$small->getWrappedObject()]));

        $this->getVariant($product)->shouldReturn(null);
    }

    function it_returns_null_without_variant_in_the_query(RequestStack $requestStack, ProductInterface $product)
    {
        $requestStack->getMainRequest()->willReturn(new Request());

        $this->getVariant($product)->shouldReturn(null);
    }

    function it_returns_null_without_request(RequestStack $requestStack, ProductInterface $product)
    {
        $requestStack->getMainRequest()->willReturn(null);

        $this->getVariant($product)->shouldReturn(null);
    }
}

<?php

namespace spec\Dedi\SyliusSEOPlugin\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\ProductSubjectFetcher;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ProductSubjectFetcherSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext, LocaleContextInterface $localeContext, ProductRepositoryInterface $repository)
    {
        $this->beConstructedWith($channelContext, $localeContext, $repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductSubjectFetcher::class);
    }

    function it_fetches(RichSnippetSubjectInterface $product, ProductRepositoryInterface $repository)
    {
        $repository->find(12)->willReturn($product);

        $this->fetch(12)->shouldReturn($product);
    }

    function it_can_from_request_with_right_route(Request $request, ParameterBag $attributes)
    {
        $attributes->get('_route')->willReturn('sylius_shop_product_show');
        $request->attributes = $attributes;

        $this->canFromRequest($request)->shouldReturn(true);
    }

    function it_can_t_from_request_with_wrong_route(Request $request, ParameterBag $attributes)
    {
        $attributes->get('_route')->willReturn('sylius_shop_contact_request');
        $request->attributes = $attributes;

        $this->canFromRequest($request)->shouldReturn(false);
    }

    function it_fetches_from_request(Request $request, ParameterBag $attributes, ChannelContextInterface $channelContext, LocaleContextInterface $localeContext, ProductRepositoryInterface $repository, ChannelInterface $channel)
    {
        $product = \Mockery::mock(ProductInterface::class, RichSnippetSubjectInterface::class);

        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocaleCode()->willReturn('fr_FR');

        $attributes->get('slug')->willReturn('mon-super-produit');
        $request->attributes = $attributes;

        $repository->findOneByChannelAndSlug($channel, 'fr_FR', 'mon-super-produit')
            ->willReturn($product);

        $this->fetchFromRequest($request)->shouldReturn($product);
    }
}

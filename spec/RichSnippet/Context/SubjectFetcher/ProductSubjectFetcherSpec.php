<?php

namespace spec\Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher\ProductSubjectFetcher;
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
    function let(FilterInterface $filter, ChannelContextInterface $channelContext, LocaleContextInterface $localeContext, ProductRepositoryInterface $repository)
    {
        $this->beConstructedWith($channelContext, $localeContext, $repository, $filter);
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

    function it_support_with_right_route(Request $request, FilterInterface $filter)
    {
        $filter->isSatisfiedBy($request)->willReturn(true);
        $this->supports($request)->shouldReturn(true);
    }

    function it_does_not_support_with_wrong_route(Request $request, FilterInterface $filter)
    {
        $filter->isSatisfiedBy($request)->willReturn(false);
        $this->supports($request)->shouldReturn(false);
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

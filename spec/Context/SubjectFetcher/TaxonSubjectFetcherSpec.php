<?php

namespace spec\Dedi\SyliusSEOPlugin\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\TaxonSubjectFetcher;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class TaxonSubjectFetcherSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext, TaxonRepositoryInterface $repository)
    {
        $this->beConstructedWith($localeContext, $repository, $repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TaxonSubjectFetcher::class);
    }

    function it_fetches(RichSnippetSubjectInterface $taxon, TaxonRepositoryInterface $repository)
    {
        $repository->find(123)->willReturn($taxon);

        $this->fetch(123)->shouldReturn($taxon);
    }

    function it_can_from_request_with_right_route(Request $request, ParameterBag $attributes)
    {
        $attributes->get('_route')->willReturn('sylius_shop_product_index');
        $request->attributes = $attributes;

        $this->canFromRequest($request)->shouldReturn(true);
    }

    function it_can_t_from_request_with_wrong_route(Request $request, ParameterBag $attributes)
    {
        $attributes->get('_route')->willReturn('sylius_shop_contact_request');
        $request->attributes = $attributes;

        $this->canFromRequest($request)->shouldReturn(false);
    }

    function it_fetches_from_request(Request $request, ParameterBag $attributes, LocaleContextInterface $localeContext, TaxonRepositoryInterface $repository)
    {
        $taxon = \Mockery::mock(TaxonInterface::class, RichSnippetSubjectInterface::class);

        $localeContext->getLocaleCode()->willReturn('fr_FR');

        $attributes->get('slug')->willReturn('mon-super-taxon');
        $request->attributes = $attributes;

        $repository->findOneBySlug('mon-super-taxon', 'fr_FR')
            ->willReturn($taxon);

        $this->fetchFromRequest($request)->shouldReturn($taxon);
    }
}

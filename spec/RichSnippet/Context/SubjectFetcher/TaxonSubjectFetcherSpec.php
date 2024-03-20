<?php

namespace spec\Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher\TaxonSubjectFetcher;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class TaxonSubjectFetcherSpec extends ObjectBehavior
{
    function let(FilterInterface $filter, LocaleContextInterface $localeContext, TaxonRepositoryInterface $repository)
    {
        $this->beConstructedWith($filter, $localeContext, $repository, $repository);
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

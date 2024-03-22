<?php

declare(strict_types=1);

namespace spec\Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher\HomepageSubjectFetcher;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\GenericPageRichSnippetSubject;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomepageSubjectFetcherSpec extends ObjectBehavior
{
    function let(FilterInterface $filter, TranslatorInterface $translator)
    {
        $this->beConstructedWith($filter, $translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(HomepageSubjectFetcher::class);
    }

    function it_fetches(TranslatorInterface $translator, GenericPageRichSnippetSubject $homepageSubject)
    {
        $translator->trans('sylius.ui.home')->willReturn('Home');

        $subject = $this->fetch();

        $subject->getName()->shouldReturn('Home');
        $subject->getRichSnippetSubjectType()->shouldReturn('homepage');
        $subject->getRichSnippetSubjectParent()->shouldReturn(null);
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

    function it_fetches_from_request(Request $request, TranslatorInterface $translator)
    {
        $translator->trans('sylius.ui.home')->willReturn('Home');

        $subject = $this->fetchFromRequest($request);

        $subject->getName()->shouldReturn('Home');
        $subject->getRichSnippetSubjectType()->shouldReturn('homepage');
        $subject->getRichSnippetSubjectParent()->shouldReturn(null);
    }
}

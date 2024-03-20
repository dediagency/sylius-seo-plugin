<?php

namespace spec\Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher\ContactSubjectFetcher;
use Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher\HomepageSubjectFetcher;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\GenericPageRichSnippetSubject;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactSubjectFetcherSpec extends ObjectBehavior
{
    function let(FilterInterface $filter, HomepageSubjectFetcher $homepageSubjectFetcher, TranslatorInterface $translator)
    {
        $this->beConstructedWith($filter, $homepageSubjectFetcher, $translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContactSubjectFetcher::class);
    }

    function it_fetches(HomepageSubjectFetcher $homepageSubjectFetcher, TranslatorInterface $translator, GenericPageRichSnippetSubject $homepageSubject)
    {
        $translator->trans('sylius.ui.contact_us')->willReturn('Contactez nous');
        $homepageSubjectFetcher->fetch()->willReturn($homepageSubject);

        $subject = $this->fetch();

        $subject->getName()->shouldReturn('Contactez nous');
        $subject->getRichSnippetSubjectType()->shouldReturn('contact');
        $subject->getRichSnippetSubjectParent()->shouldReturn($homepageSubject);
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

    function it_fetches_from_request(Request $request, HomepageSubjectFetcher $homepageSubjectFetcher, TranslatorInterface $translator, GenericPageRichSnippetSubject $homepageSubject)
    {
        $translator->trans('sylius.ui.contact_us')->willReturn('Contactez nous');
        $homepageSubjectFetcher->fetch()->willReturn($homepageSubject);

        $subject = $this->fetchFromRequest($request);

        $subject->getName()->shouldReturn('Contactez nous');
        $subject->getRichSnippetSubjectType()->shouldReturn('contact');
        $subject->getRichSnippetSubjectParent()->shouldReturn($homepageSubject);
    }
}

<?php

namespace spec\Dedi\SyliusSEOPlugin\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher\HomepageSubjectFetcher;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\GenericPageRichSnippetSubject;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomepageSubjectFetcherSpec extends ObjectBehavior
{
    function let(TranslatorInterface $translator)
    {
        $this->beConstructedWith($translator);
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

    function it_can_from_request_with_right_route(Request $request, ParameterBag $attributes)
    {
        $attributes->get('_route')->willReturn('sylius_shop_homepage');
        $request->attributes = $attributes;

        $this->canFromRequest($request)->shouldReturn(true);
    }

    function it_can_t_from_request_with_wrong_route(Request $request, ParameterBag $attributes)
    {
        $attributes->get('_route')->willReturn('sylius_shop_contact_request');
        $request->attributes = $attributes;

        $this->canFromRequest($request)->shouldReturn(false);
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

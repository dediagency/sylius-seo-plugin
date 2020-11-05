<?php

namespace spec\Dedi\SyliusSEOPlugin\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\ContactSubjectFetcher;
use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\HomepageSubjectFetcher;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\Subject\GenericPageRichSnippetSubject;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactSubjectFetcherSpec extends ObjectBehavior
{
    function let(HomepageSubjectFetcher $homepageSubjectFetcher, TranslatorInterface $translator)
    {
        $this->beConstructedWith($homepageSubjectFetcher, $translator);
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

    function it_can_from_request_with_right_route(Request $request, ParameterBag $attributes)
    {
        $attributes->get('_route')->willReturn('sylius_shop_contact_request');
        $request->attributes = $attributes;

        $this->canFromRequest($request)->shouldReturn(true);
    }

    function it_can_t_from_request_with_wrong_route(Request $request, ParameterBag $attributes)
    {
        $attributes->get('_route')->willReturn('sylius_shop_homepage');
        $request->attributes = $attributes;

        $this->canFromRequest($request)->shouldReturn(false);
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

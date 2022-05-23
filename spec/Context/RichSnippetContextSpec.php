<?php

namespace spec\Dedi\SyliusSEOPlugin\Context;

use Dedi\SyliusSEOPlugin\Context\RichSnippetContext;
use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\SubjectFetcherInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetFactoryInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RichSnippetContextSpec extends ObjectBehavior
{
    function let(
        RequestStack $requestStack,
        Request $request,
        SubjectFetcherInterface $subjectFetcherA,
        SubjectFetcherInterface $subjectFetcherB,
        SubjectFetcherInterface $subjectFetcherC,
        RichSnippetFactoryInterface $snippetFactoryA,
        RichSnippetFactoryInterface $snippetFactoryB,
        RichSnippetFactoryInterface $snippetFactoryC,
        RichSnippetFactoryInterface $snippetFactoryD
    ) {
        $requestStack->getMainRequest()->willReturn($request);

        $this->beConstructedWith(
            $requestStack,
            new \ArrayIterator([
                $subjectFetcherA->getWrappedObject(),
                $subjectFetcherB->getWrappedObject(),
                $subjectFetcherC->getWrappedObject(),
            ]),
            new \ArrayIterator([
                $snippetFactoryA->getWrappedObject(),
                $snippetFactoryB->getWrappedObject(),
                $snippetFactoryC->getWrappedObject(),
                $snippetFactoryD->getWrappedObject(),
            ])
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RichSnippetContext::class);
    }

    function it_gets_no_rich_snippets(
        Request $request,
        SubjectFetcherInterface $subjectFetcherA,
        SubjectFetcherInterface $subjectFetcherB,
        SubjectFetcherInterface $subjectFetcherC
    ) {
        $subjectFetcherA->canFromRequest($request)->willReturn(false);
        $subjectFetcherB->canFromRequest($request)->willReturn(false);
        $subjectFetcherC->canFromRequest($request)->willReturn(false);

        $this->getAvailableRichSnippets()->shouldReturn([]);
    }

    function it_gets_the_available_rich_snippets(
        Request $request,
        SubjectFetcherInterface $subjectFetcherA,
        SubjectFetcherInterface $subjectFetcherB,
        SubjectFetcherInterface $subjectFetcherC,
        RichSnippetFactoryInterface $snippetFactoryA,
        RichSnippetFactoryInterface $snippetFactoryB,
        RichSnippetFactoryInterface $snippetFactoryC,
        RichSnippetFactoryInterface $snippetFactoryD,
        RichSnippetSubjectInterface $subject,
        RichSnippetInterface $richSnippetB,
        RichSnippetInterface $richSnippetD
    ) {
        $subjectFetcherA->canFromRequest($request)->willReturn(false);
        $subjectFetcherB->canFromRequest($request)->willReturn(true);
        $subjectFetcherC->canFromRequest($request)->shouldNotBeCalled();

        $subjectFetcherB->fetchFromRequest($request)->willReturn($subject);

        $snippetFactoryA->can($subject)->willReturn(false);
        $snippetFactoryB->can($subject)->willReturn(true);
        $snippetFactoryC->can($subject)->willReturn(false);
        $snippetFactoryD->can($subject)->willReturn(true);

        $snippetFactoryA->buildRichSnippet($subject)->shouldNotBeCalled();
        $snippetFactoryB->buildRichSnippet($subject)->willReturn($richSnippetB);
        $snippetFactoryC->buildRichSnippet($subject)->shouldNotBeCalled();
        $snippetFactoryD->buildRichSnippet($subject)->willReturn($richSnippetD);

        $this->getAvailableRichSnippets()->shouldReturn([$richSnippetB, $richSnippetD]);
    }
}

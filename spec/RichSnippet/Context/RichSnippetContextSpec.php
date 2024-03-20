<?php

namespace spec\Dedi\SyliusSEOPlugin\RichSnippet\Context;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Context\RichSnippetContext;
use Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher\SubjectFetcherInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Factory\RichSnippetFactoryInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet\RichSnippetInterface;
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
        $subjectFetcherA->supports($request)->willReturn(false);
        $subjectFetcherB->supports($request)->willReturn(false);
        $subjectFetcherC->supports($request)->willReturn(false);

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
        $subjectFetcherA->supports($request)->willReturn(false);
        $subjectFetcherB->supports($request)->willReturn(true);
        $subjectFetcherC->supports($request)->shouldNotBeCalled();

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

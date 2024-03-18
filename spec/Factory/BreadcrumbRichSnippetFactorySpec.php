<?php

namespace spec\Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher\HomepageSubjectFetcher;
use Dedi\SyliusSEOPlugin\RichSnippet\Factory\BreadcrumbRichSnippetFactory;
use Dedi\SyliusSEOPlugin\RichSnippet\Factory\RichSnippetSubjectUrlFactoryInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\HomepageRichSnippetSubject;
use PhpSpec\ObjectBehavior;

class BreadcrumbRichSnippetFactorySpec extends ObjectBehavior
{
    function let(RichSnippetSubjectUrlFactoryInterface $richSnippetSubjectUrlFactory, HomepageSubjectFetcher $homepageSubjectFetcher)
    {
        $this->beConstructedWith($richSnippetSubjectUrlFactory, $homepageSubjectFetcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BreadcrumbRichSnippetFactory::class);
    }

    function it_can_handle_valid_subject(RichSnippetSubjectInterface $subject)
    {
        $subject->getRichSnippetSubjectType()->willReturn('homepage');

        $this->can($subject)->shouldReturn(true);

        $subject->getRichSnippetSubjectType()->willReturn('contact');

        $this->can($subject)->shouldReturn(true);

        $subject->getRichSnippetSubjectType()->willReturn('taxon');

        $this->can($subject)->shouldReturn(true);

        $subject->getRichSnippetSubjectType()->willReturn('product');

        $this->can($subject)->shouldReturn(true);
    }

    function it_can_t_handle_invalid_subject(RichSnippetSubjectInterface $subject)
    {
        $subject->getRichSnippetSubjectType()->willReturn('unknown_subject_type');

        $this->can($subject)->shouldReturn(false);
    }

    function it_builds_rich_snippets(
        RichSnippetSubjectUrlFactoryInterface $richSnippetSubjectUrlFactory,
        HomepageSubjectFetcher $homepageSubjectFetcher,
        RichSnippetSubjectInterface $child,
        RichSnippetSubjectInterface $parent,
        RichSnippetSubjectInterface $grandParent,
        HomepageRichSnippetSubject $homepage
    ) {
        $child->getRichSnippetSubjectParent()->willReturn($parent);
        $parent->getRichSnippetSubjectParent()->willReturn($grandParent);
        $grandParent->getRichSnippetSubjectParent()->willReturn(null);
        $homepage->getRichSnippetSubjectParent()->willReturn(null);

        $homepageSubjectFetcher->fetch()->willReturn($homepage);

        $child->getName()->willReturn('X-wing');
        $parent->getName()->willReturn('Spaceships');
        $grandParent->getName()->willReturn('Star Wars');
        $homepage->getName()->willReturn('Home');

        $richSnippetSubjectUrlFactory->buildUrl($parent)->willReturn('/my-shop/star-wars/spaceships');
        $richSnippetSubjectUrlFactory->buildUrl($grandParent)->willReturn('/my-shop/star-wars');
        $richSnippetSubjectUrlFactory->buildUrl($homepage)->willReturn('/my-shop');

        $richSnippet = $this->buildRichSnippet($child);
        $richSnippet->getData()->shouldReturn([
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => '/my-shop'
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => 'Star Wars',
                        'item' => '/my-shop/star-wars'
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => 'Spaceships',
                        'item' => '/my-shop/star-wars/spaceships'
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 4,
                        'name' => 'X-wing',
                    ],
                ],
            ]
        ]);
    }
}

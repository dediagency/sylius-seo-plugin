<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\SubjectFetcherInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\AbstractRichSnippetFactory;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetSubjectUrlFactoryInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippet\BreadcrumbRichSnippet;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\Subject\HomepageRichSnippetSubject;

final class BreadcrumbRichSnippetFactory extends AbstractRichSnippetFactory
{
    protected RichSnippetSubjectUrlFactoryInterface $richSnippetSubjectUrlFactory;

    protected SubjectFetcherInterface  $homepageSubjectFetcher;

    public function __construct(
        RichSnippetSubjectUrlFactoryInterface $richSnippetSubjectUrlFactory,
        SubjectFetcherInterface $homepageSubjectFetcher,
    ) {
        $this->richSnippetSubjectUrlFactory = $richSnippetSubjectUrlFactory;
        $this->homepageSubjectFetcher = $homepageSubjectFetcher;
    }

    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface
    {
        return $this->build($subject, new BreadcrumbRichSnippet(), true);
    }

    private function build(
        RichSnippetSubjectInterface $subject,
        RichSnippetInterface $richSnippet,
        bool $isLeaf = false,
    ): RichSnippetInterface {
        if (null !== $parent = $subject->getRichSnippetSubjectParent()) {
            $this->build($parent, $richSnippet);
        } elseif (!$subject instanceof HomepageRichSnippetSubject) {
            $richSnippetSubject = $this->homepageSubjectFetcher->fetch();
            if (null !== $richSnippetSubject) {
                $this->build($richSnippetSubject, $richSnippet);
            }
        }

        $richSnippet->addData([
            'name' => $subject->getName(),
            'item' => $isLeaf ? null : $this->richSnippetSubjectUrlFactory->buildUrl($subject),
        ]);

        return $richSnippet;
    }

    protected function getHandledSubjectTypes(): array
    {
        return [
            'homepage',
            'contact',
            'taxon',
            'product',
        ];
    }
}

<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\HomepageSubjectFetcher;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetFactoryInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetSubjectUrlFactory;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\BreadcrumbRichSnippet;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\HomepageRichSnippetSubject;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;

final class BreadcrumbRichSnippetFactory implements RichSnippetFactoryInterface
{
    public const TYPE = 'breadcrumb';

    private RichSnippetSubjectUrlFactory $richSnippetSubjectUrlFactory;
    private HomepageSubjectFetcher $homepageSubjectFetcher;

    public function __construct(
        RichSnippetSubjectUrlFactory $richSnippetSubjectUrlFactory,
        HomepageSubjectFetcher $homepageSubjectFetcher
    ) {
        $this->richSnippetSubjectUrlFactory = $richSnippetSubjectUrlFactory;
        $this->homepageSubjectFetcher = $homepageSubjectFetcher;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function can(string $type, RichSnippetSubjectInterface $subject): bool
    {
        return self::TYPE === $type;
    }

    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface
    {
        return $this->build($subject, new BreadcrumbRichSnippet(), true);
    }

    private function build(
        RichSnippetSubjectInterface $subject,
        BreadcrumbRichSnippet $richSnippet,
        bool $isLead = false
    ): BreadcrumbRichSnippet {
        if ($parent = $subject->getParent()) {
            $this->build($parent, $richSnippet);
        } elseif (!$subject instanceof HomepageRichSnippetSubject) {
            $this->build($this->homepageSubjectFetcher->fetch(HomepageSubjectFetcher::TYPE), $richSnippet);
        }

        $richSnippet->addElement(
            $subject->getName(),
            $isLead ? null : $this->richSnippetSubjectUrlFactory->buildUrl($subject)
        );

        return $richSnippet;
    }
}

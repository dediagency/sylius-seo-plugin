<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Factory\SubjectUrl;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\SubjectUrl\SubjectUrlGeneratorInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\Subject\HomepageRichSnippetSubject;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class HomepageUrlGenerator implements SubjectUrlGeneratorInterface
{
    public function __construct(
        protected readonly RouterInterface $router
    ) {
    }

    public function can(RichSnippetSubjectInterface $subject): bool
    {
        return $subject instanceof HomepageRichSnippetSubject;
    }

    public function generateUrl(RichSnippetSubjectInterface $subject): string
    {
        return $this->router->generate('sylius_shop_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}

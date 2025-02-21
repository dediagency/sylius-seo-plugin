<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\HomepageRichSnippetSubject;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class HomepageUrlGenerator implements SubjectUrlGeneratorInterface
{
    public function __construct(private readonly RouterInterface $router)
    {
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

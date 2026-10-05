<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;

interface SubjectUrlGeneratorInterface
{
    public function can(RichSnippetSubjectInterface $subject): bool;

    public function generateUrl(RichSnippetSubjectInterface $subject): string;
}

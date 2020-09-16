<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Factory\SubjectUrl;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;

interface SubjectUrlGeneratorInterface
{
    public function can(RichSnippetSubjectInterface $subject): bool;

    public function generateUrl(RichSnippetSubjectInterface $subject): string;
}

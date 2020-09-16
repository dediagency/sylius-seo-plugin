<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;

interface RichSnippetSubjectUrlFactoryInterface
{
    public function buildUrl(RichSnippetSubjectInterface $subject): string;
}

<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Factory;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;

interface RichSnippetSubjectUrlFactoryInterface
{
    public function buildUrl(RichSnippetSubjectInterface $subject): string;
}

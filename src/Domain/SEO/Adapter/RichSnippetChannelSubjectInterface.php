<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

interface RichSnippetChannelSubjectInterface extends RichSnippetSubjectInterface
{
    public function getCode(): ?string;

    public function getName(): ?string;

    public function getDescription(): ?string;
}

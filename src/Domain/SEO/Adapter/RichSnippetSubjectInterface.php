<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

interface RichSnippetSubjectInterface
{
    /**
     * @return int|null
     */
    public function getId();

    public function getRichSnippetSubjectType(): string;

    /**
     * @return string
     */
    public function getName();

    /**
     * @return self|null
     */
    public function getParent();
}

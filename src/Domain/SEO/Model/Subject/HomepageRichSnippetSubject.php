<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Model\Subject;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;

class HomepageRichSnippetSubject implements RichSnippetSubjectInterface
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return null;
    }

    public function getRichSnippetSubjectType(): string
    {
        return 'homepage';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRichSnippetSubjectParent(): ?RichSnippetSubjectInterface
    {
        return null;
    }
}

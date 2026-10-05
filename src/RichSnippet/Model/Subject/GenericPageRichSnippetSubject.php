<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;

class GenericPageRichSnippetSubject implements RichSnippetSubjectInterface
{
    public function __construct(private string $name, private string $type, private RichSnippetSubjectInterface $parent)
    {
    }

    public function getId(): ?int
    {
        return null;
    }

    public function getRichSnippetSubjectType(): string
    {
        return $this->type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRichSnippetSubjectParent(): ?RichSnippetSubjectInterface
    {
        return $this->parent;
    }
}

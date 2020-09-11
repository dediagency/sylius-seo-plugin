<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Model;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;

class GenericPageRichSnippetSubject implements RichSnippetSubjectInterface
{
    private string $name;
    private string $type;
    private RichSnippetSubjectInterface $parent;

    public function __construct(string $name, string $type, RichSnippetSubjectInterface $parent)
    {
        $this->name = $name;
        $this->type = $type;
        $this->parent = $parent;
    }

    public function getId()
    {
        return null;
    }

    public function getRichSnippetSubjectType(): string
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParent()
    {
        return $this->parent;
    }
}

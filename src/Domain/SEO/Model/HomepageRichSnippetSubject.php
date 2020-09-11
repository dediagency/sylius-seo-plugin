<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Model;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;

class HomepageRichSnippetSubject implements RichSnippetSubjectInterface
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getId()
    {
        return null;
    }

    public function getRichSnippetSubjectType(): string
    {
        return 'homepage';
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParent()
    {
        return null;
    }
}

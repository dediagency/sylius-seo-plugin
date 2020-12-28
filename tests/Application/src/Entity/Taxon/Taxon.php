<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Application\src\Entity\Taxon;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Taxon as BaseTaxon;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_taxon")
 */
class Taxon extends BaseTaxon implements RichSnippetSubjectInterface
{
    public function getRichSnippetSubjectType(): string
    {
        return 'taxon';
    }

    public function getRichSnippetSubjectParent(): ?RichSnippetSubjectInterface
    {
        return $this->getParent();
    }
}

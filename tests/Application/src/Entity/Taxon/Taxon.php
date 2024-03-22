<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Application\src\Entity\Taxon;

use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableTaxonTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Taxon as BaseTaxon;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="sylius_taxon")
 */
class Taxon extends BaseTaxon implements ReferenceableInterface, RichSnippetSubjectInterface
{
    use ReferenceableTaxonTrait;

    public function getRichSnippetSubjectType(): string
    {
        return 'taxon';
    }

    public function getRichSnippetSubjectParent(): ?RichSnippetSubjectInterface
    {
        return $this->getParent();
    }

    protected function createReferenceableContent(): SEOContentInterface
    {
        return new SEOContent();
    }
}

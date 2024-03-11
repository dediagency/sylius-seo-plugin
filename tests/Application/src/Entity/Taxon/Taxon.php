<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Application\src\Entity\Taxon;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\MetadataTagInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableTrait;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Taxon as BaseTaxon;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="sylius_taxon")
 */
class Taxon extends BaseTaxon implements ReferenceableInterface, RichSnippetSubjectInterface
{
    use ReferenceableTrait;

    public function getRichSnippetSubjectType(): string
    {
        return 'taxon';
    }

    public function getRichSnippetSubjectParent(): ?RichSnippetSubjectInterface
    {
        return $this->getParent();
    }

    protected function createReferenceableContent(): MetadataTagInterface
    {
        return new SEOContent();
    }
}

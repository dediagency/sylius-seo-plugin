<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Adapter;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Doctrine\ORM\Mapping as ORM;

trait ReferenceableChannelTrait
{
    use ReferenceableTrait;

    /**
     * @ORM\OneToOne(inversedBy="channel", targetEntity="Dedi\SyliusSEOPlugin\Entity\SEOContentInterface", cascade={"persist", "remove"})
     *
     * @ORM\JoinColumn(name="referenceableContent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    #[ORM\OneToOne(inversedBy: 'channel', targetEntity: SEOContentInterface::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'referenceableContent_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    protected ?SEOContentInterface $referenceableContent = null;
}
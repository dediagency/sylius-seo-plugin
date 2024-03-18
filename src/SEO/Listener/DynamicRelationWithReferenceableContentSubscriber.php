<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Listener;

use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableAwareInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class DynamicRelationWithReferenceableContentSubscriber implements EventSubscriber
{
    public const REFERENCIABLE_FIELD_NAME = 'referenceableContent';

    public function getSubscribedEvents(): array
    {
        return [Events::loadClassMetadata];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();
        if (
            !$metadata->getReflectionClass()->implementsInterface(ReferenceableAwareInterface::class) ||
            !$metadata->getReflectionClass()->hasProperty(self::REFERENCIABLE_FIELD_NAME)
        ) {
            return;
        }

        $namingStrategy = $eventArgs
            ->getEntityManager()
            ->getConfiguration()
            ->getNamingStrategy()
        ;

        $metadata->mapOneToOne([
            'targetEntity' => SEOContent::class,
            'fieldName' => self::REFERENCIABLE_FIELD_NAME,
            'cascade' => ['persist', 'remove'],
            'joinTable' => [
                'name' => strtolower($namingStrategy->classToTableName($metadata->getName())) . '_referenceable_content_id',
                'referencedColumnName' => $namingStrategy->referenceColumnName(),
            ],
        ]);
    }
}

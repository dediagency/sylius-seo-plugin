<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Context;

use Dedi\SyliusSEOPlugin\SEO\Builder\MetadataDirectorInterface;
use Dedi\SyliusSEOPlugin\SEO\Exception\ContextNotFoundException;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;
use Exception;
use Laminas\Stdlib\PriorityQueue;

class CompositeMetadataContext implements MetadataContextInterface
{
    /** @var PriorityQueue<MetadataContextInterface, int> */
    private PriorityQueue $metadataContexts;

    public function __construct(private MetadataDirectorInterface $metadataDirector)
    {
        $this->metadataContexts = new PriorityQueue();
    }

    public function addContext(MetadataContextInterface $context, int $priority = 0): void
    {
        $this->metadataContexts->insert($context, $priority);
    }

    public function getMetadata(): Metadata
    {
        $lastException = null;
        $metadata = [];

        foreach ($this->metadataContexts as $context) {
            try {
                $metadata[] = $context->getMetadata();
            } catch (Exception $exception) {
                $lastException = $exception;

                continue;
            }
        }

        if (count($metadata) === 0) {
            throw new ContextNotFoundException(null, $lastException);
        }

        return $this->metadataDirector->buildHierarchical($metadata);
    }
}

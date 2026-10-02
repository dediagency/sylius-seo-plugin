<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Api\Processor;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\Enum\SEOContentTypeEnum;

/** @implements ProcessorInterface<SEOContentInterface, SEOContentInterface> */
class SEOContentProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<SEOContentInterface, SEOContentInterface> $persistProcessor
     * @param ProcessorInterface<SEOContentInterface, SEOContentInterface> $removeProcessor
     */
    public function __construct(
        private readonly ProcessorInterface $persistProcessor,
        private readonly ProcessorInterface $removeProcessor,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): SEOContentInterface
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        if ($operation instanceof Post) {
            $data->setType(SEOContentTypeEnum::fromSEOContent($data)?->value);
        }

        $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        return $data;
    }
}

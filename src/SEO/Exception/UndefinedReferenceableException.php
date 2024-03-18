<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Exception;

final class UndefinedReferenceableException extends \RuntimeException
{
    public function __construct(?string $message = null, ?\Exception $previousException = null)
    {
        parent::__construct($message ?? 'Entity has no associated referenceable data', 0, $previousException);
    }
}

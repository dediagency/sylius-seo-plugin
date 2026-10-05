<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Exception;

final class ContextNotAvailableInRequestException extends \RuntimeException
{
    public function __construct(?string $message = null, ?\Exception $previousException = null)
    {
        parent::__construct($message ?? 'Metadata context not available for current request', 0, $previousException);
    }
}

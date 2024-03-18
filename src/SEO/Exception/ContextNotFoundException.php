<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Exception;

final class ContextNotFoundException extends \RuntimeException
{
    public function __construct(?string $message = null, ?\Exception $previousException = null)
    {
        parent::__construct($message ?? 'Metadata context not found', 0, $previousException);
    }
}

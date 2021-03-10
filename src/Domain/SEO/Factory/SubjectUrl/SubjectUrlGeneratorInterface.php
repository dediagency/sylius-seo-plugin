<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Factory\SubjectUrl;

interface SubjectUrlGeneratorInterface
{
    public function can($subject): bool;

    public function generateUrl($subject): string;
}

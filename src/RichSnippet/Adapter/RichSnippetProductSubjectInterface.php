<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Adapter;

interface RichSnippetProductSubjectInterface extends RichSnippetSubjectInterface
{
    public function getSEOBrand(): ?string;

    public function getSEOGtin8(): ?string;

    public function getSEOGtin13(): ?string;

    public function getSEOGtin14(): ?string;

    public function getSEOMpn(): ?string;

    public function getSEOIsbn(): ?string;

    public function getSEOSku(): ?string;
}

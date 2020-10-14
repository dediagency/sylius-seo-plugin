<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

interface RichSnippetProductSubjectInterface extends RichSnippetSubjectInterface
{
    public function getBrand(): ?string;

    public function getGtin8(): ?string;

    public function getGtin13(): ?string;

    public function getGtin14(): ?string;

    public function getMpn(): ?string;

    public function getIsbn(): ?string;

    public function getSku(): ?string;

    public function isOfferAggregated(): bool;
}

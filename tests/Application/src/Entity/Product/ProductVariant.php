<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Application\src\Entity\Product;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductVariantSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductVariantSubjectTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_product_variant')]
class ProductVariant extends BaseProductVariant implements RichSnippetProductVariantSubjectInterface
{
    use RichSnippetProductVariantSubjectTrait;
}

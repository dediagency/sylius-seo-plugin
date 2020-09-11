<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Application\src\Form\Extension;

use Dedi\SyliusSEOPlugin\Form\Extension\AbstractReferenceableTypeExtension;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;

class ProductTypeExtension extends AbstractReferenceableTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }
}

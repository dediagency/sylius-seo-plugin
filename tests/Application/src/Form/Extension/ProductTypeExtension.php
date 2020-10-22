<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Application\src\Form\Extension;

use Dedi\SyliusSEOPlugin\Form\Extension\AbstractRichSnippetProductSubjectTypeExtension;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;

class ProductTypeExtension extends AbstractRichSnippetProductSubjectTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }
}

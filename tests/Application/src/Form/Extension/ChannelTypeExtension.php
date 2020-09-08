<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Application\src\Form\Extension;

use Dedi\SyliusSEOPlugin\Form\Extension\AbstractReferenceableTypeExtension;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;

class ChannelTypeExtension extends AbstractReferenceableTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ChannelType::class];
    }
}

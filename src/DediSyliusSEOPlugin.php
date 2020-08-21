<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class DediSyliusSEOPlugin extends Bundle
{
    use SyliusPluginTrait;
}

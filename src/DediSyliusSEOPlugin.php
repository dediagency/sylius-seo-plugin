<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin;

use Dedi\SyliusSEOPlugin\DependencyInjection\Compiler\CompositeMetadataContextPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class DediSyliusSEOPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompositeMetadataContextPass());
    }
}

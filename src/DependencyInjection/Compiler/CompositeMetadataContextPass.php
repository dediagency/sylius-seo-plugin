<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\DependencyInjection\Compiler;

use Dedi\SyliusSEOPlugin\SEO\Context\MetadataContextInterface;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CompositeMetadataContextPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'dedi_sylius_seo_plugin.context.metadata',
            'dedi_sylius_seo_plugin.context.metadata.composite',
            'dedi_sylius_seo_plugin.context.metadata',
            'addContext',
        );
    }

    public function process(ContainerBuilder $container): void
    {
        parent::process($container);

        $container->setAlias(MetadataContextInterface::class, 'dedi_sylius_seo_plugin.context.metadata');
    }
}

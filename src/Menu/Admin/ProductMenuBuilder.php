<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Menu\Admin;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class ProductMenuBuilder
{
    public function addSeoMenuItem(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $menu->addChild('seo')
            ->setAttribute('template', '@DediSyliusSEOPlugin/Admin/Product/Tab/_seo.html.twig')
            ->setLabel('dedi_sylius_seo_plugin.ui.seo')
        ;
    }
}

<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Listener\Admin;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class SEOMenuBuilder
{
    public function addItem(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu()->getChild('marketing');

        if ($menu instanceof ItemInterface) {
            $menu
                ->addChild('seo', ['route' => 'dedi_sylius_seo_plugin_admin_seo_content_index'])
                ->setLabel('dedi_sylius_seo_plugin.ui.seo')
                ->setLabelAttribute('icon', 'search')
            ;
        }
    }
}

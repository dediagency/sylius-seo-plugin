<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop;

class ProductPage extends AbstractSeoPage
{
    public function getCurrentUrl(): string
    {
        return $this->getSession()->getCurrentUrl();
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_product_show';
    }
}

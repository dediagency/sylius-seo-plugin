<?php

declare(strict_types=1);

namespace spec\Dedi\SyliusSEOPlugin\Filter\Specification;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\Filter\Specification\IsProductFilter;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;

class IsProductFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IsProductFilter::class);
    }

    function it_implements_filter_interface()
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_returns_true_if_request_route_is_sylius_shop_product_show()
    {
        $request = new Request([], [], ['_route' => 'sylius_shop_product_show']);
        $this->isSatisfiedBy($request)->shouldReturn(true);
    }

    function it_returns_false_if_request_route_is_not_sylius_shop_product_show()
    {
        $request = new Request([], [], ['_route' => 'some_other_route']);
        $this->isSatisfiedBy($request)->shouldReturn(false);
    }
}

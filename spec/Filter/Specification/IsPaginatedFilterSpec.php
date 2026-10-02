<?php

declare(strict_types=1);

namespace spec\Dedi\SyliusSEOPlugin\Filter\Specification;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\Filter\Specification\IsPaginatedFilter;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;

class IsPaginatedFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IsPaginatedFilter::class);
    }

    function it_implements_filter_interface()
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_returns_false_if_page_query_parameter_is_not_present()
    {
        $request = new Request([]);
        $this->isSatisfiedBy($request)->shouldReturn(false);
    }

    function it_returns_false_if_page_query_parameter_is_1()
    {
        $request = new Request(['page' => '1']);
        $this->isSatisfiedBy($request)->shouldReturn(false);
    }

    function it_returns_true_if_page_query_parameter_is_greater_than_1()
    {
        $request = new Request(['page' => '2']);
        $this->isSatisfiedBy($request)->shouldReturn(true);
    }

    function it_returns_false_if_page_query_parameter_is_less_than_1()
    {
        $request = new Request(['page' => '0']);
        $this->isSatisfiedBy($request)->shouldReturn(false);
    }
}

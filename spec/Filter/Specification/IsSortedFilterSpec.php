<?php

declare(strict_types=1);

namespace spec\Dedi\SyliusSEOPlugin\Filter\Specification;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\Filter\Specification\IsSortedFilter;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;

class IsSortedFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IsSortedFilter::class);
    }

    function it_implements_filter_interface()
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_returns_true_if_sorting_query_parameter_exists()
    {
        $request = new Request(['sorting' => ['field' => 'value']]);
        $this->isSatisfiedBy($request)->shouldReturn(true);
    }

    function it_returns_false_if_sorting_query_parameter_does_not_exist()
    {
        $request = new Request([]);
        $this->isSatisfiedBy($request)->shouldReturn(false);
    }
}

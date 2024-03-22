<?php

declare(strict_types=1);

namespace spec\Dedi\SyliusSEOPlugin\Filter\Specification;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\Filter\Specification\IsPaginatedFilter;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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

    function it_returns_true_if_page_query_parameter_is_not_present(
        Request $request,
        ParameterBagInterface $parameterBag,
    ) {
        $parameterBag->get('page')->willReturn(null);
        $request->query = $parameterBag;
        $this->isSatisfiedBy($request)->shouldReturn(true);
    }

    function it_returns_true_if_page_query_parameter_is_1(
        Request $request,
        ParameterBagInterface $parameterBag,
    ) {
        $parameterBag->get('page')->willReturn('1');
        $request->query = $parameterBag;
        $this->isSatisfiedBy($request)->shouldReturn(false);
    }

    function it_returns_false_if_page_query_parameter_is_greater_than_1(
        Request $request,
        ParameterBagInterface $parameterBag,
    ) {
        $parameterBag->get('page')->willReturn('2');
        $request->query = $parameterBag;
        $this->isSatisfiedBy($request)->shouldReturn(true);
    }

    function it_returns_false_if_page_query_parameter_is_less_than_1(
        Request $request,
        ParameterBagInterface $parameterBag,
    ) {
        $parameterBag->get('page')->willReturn('0');
        $request->query = $parameterBag;
        $this->isSatisfiedBy($request)->shouldReturn(false);
    }
}

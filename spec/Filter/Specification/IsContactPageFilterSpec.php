<?php

declare(strict_types=1);

namespace spec\Dedi\SyliusSEOPlugin\Filter\Specification;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\Filter\Specification\IsContactPageFilter;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class IsContactPageFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IsContactPageFilter::class);
    }

    function it_implements_filter_interface()
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_returns_true_if_request_route_is_sylius_shop_contact_request(
        Request $request,
        ParameterBag $parameterBag,
    ) {
        $parameterBag->get('_route', '')->willReturn('sylius_shop_contact_request');
        $request->attributes = $parameterBag;
        $this->isSatisfiedBy($request)->shouldReturn(true);
    }

    function it_returns_false_if_request_route_is_not_sylius_shop_contact_request(
        Request $request,
        ParameterBag $parameterBag,
    ) {
        $parameterBag->get('_route', '')->willReturn('some_other_route');
        $request->attributes = $parameterBag;
        $this->isSatisfiedBy($request)->shouldReturn(false);
    }
}

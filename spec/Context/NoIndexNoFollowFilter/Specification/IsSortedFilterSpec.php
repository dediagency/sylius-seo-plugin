<?php

namespace spec\Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\Specification;

use Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\NoIndexNoFollowFilterInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class IsSortedFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NoIndexNoFollowFilterInterface::class);
    }

    function it_return_false_when_request_have_no_sorting(Request $request, ParameterBag $query)
    {
        $query->get('sorting', '')->shouldBeCalled()->willReturn('');
        $request->query = $query;
        $this->isSatisfiedBy($request)->shouldReturn(false);
    }

    function it_return_true_when_request_have_sorting(Request $request, ParameterBag $query)
    {
        $query->get('sorting', '')->shouldBeCalled()->willReturn(1);
        $request->query = $query;
        $this->isSatisfiedBy($request)->shouldReturn(true);
    }
}

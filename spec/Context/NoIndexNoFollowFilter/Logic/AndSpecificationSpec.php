<?php

namespace spec\Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\Logic;

use Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\NoIndexNoFollowFilterInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;

class AndSpecificationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NoIndexNoFollowFilterInterface::class);
    }

    function it_should_return_false_when_at_least_one_specification_return_false(
        Request $request,
        NoIndexNoFollowFilterInterface $impl1,
        NoIndexNoFollowFilterInterface $impl2,
        NoIndexNoFollowFilterInterface $impl3): void
    {
        $impl1->isSatisfiedBy($request)->willReturn(true);
        $impl2->isSatisfiedBy($request)->willReturn(false);
        $impl3->isSatisfiedBy($request)->willReturn(true);
        $this->beConstructedWith($impl1, $impl2, $impl3);
        $this->isSatisfiedBy($request)->shouldReturn(false);
    }

    function it_should_return_false_when_all_specification_return_false(Request $request, NoIndexNoFollowFilterInterface $falseImpl): void
    {
        $falseImpl->isSatisfiedBy($request)->shouldBeCalled()->willReturn(false);
        $this->beConstructedWith($falseImpl, $falseImpl, $falseImpl);
        $this->isSatisfiedBy($request)->shouldReturn(false);
    }

    function it_should_return_true_when_all_specification_return_true(Request $request, NoIndexNoFollowFilterInterface $trueImpl): void
    {
        $trueImpl->isSatisfiedBy($request)->shouldBeCalledTimes(3)->willReturn(true);
        $this->beConstructedWith($trueImpl, $trueImpl, $trueImpl);
        $this->isSatisfiedBy($request)->shouldReturn(true);
    }
}

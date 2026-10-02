<?php

declare(strict_types=1);

namespace spec\Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\HomepageRichSnippetSubject;
use Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator\HomepageUrlGenerator;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class HomepageUrlGeneratorSpec extends ObjectBehavior
{
    function let(RouterInterface $router)
    {
        $this->beConstructedWith($router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(HomepageUrlGenerator::class);
    }

    function it_can_handle_valid_subjet(HomepageRichSnippetSubject $subject)
    {
        $this->can($subject)->shouldReturn(true);
    }

    function it_can_t_handle_invalid_subjet()
    {
        $subject = \Mockery::mock(ProductInterface::class, RichSnippetSubjectInterface::class);

        $this->can($subject)->shouldReturn(false);
    }

    function it_generates_url(RouterInterface $router, HomepageRichSnippetSubject $subject)
    {
        $router->generate('sylius_shop_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL)->willReturn('my_route');

        $this->generateUrl($subject)->shouldReturn('my_route');
    }
}

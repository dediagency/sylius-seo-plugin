<?php

namespace spec\Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\HomepageRichSnippetSubject;
use Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator\ProductUrlGenerator;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ProductUrlGeneratorSpec extends ObjectBehavior
{
    function let(RouterInterface $router)
    {
        $this->beConstructedWith($router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductUrlGenerator::class);
    }

    function it_can_handle_valid_subjet()
    {
        $subject = \Mockery::mock(ProductInterface::class, RichSnippetSubjectInterface::class);

        $this->can($subject)->shouldReturn(true);
    }

    function it_can_t_handle_invalid_subjet(HomepageRichSnippetSubject $subject)
    {
        $this->can($subject)->shouldReturn(false);
    }

    function it_generates_url(RouterInterface $router)
    {
        $subject = \Mockery::mock(ProductInterface::class, RichSnippetSubjectInterface::class);

        $subject->shouldReceive(['getSlug' => 'my_slug']);

        $router->generate('sylius_shop_product_show', ['slug' => 'my_slug'], UrlGeneratorInterface::ABSOLUTE_URL)->willReturn('my_route');

        $this->generateUrl($subject)->shouldReturn('my_route');
    }
}

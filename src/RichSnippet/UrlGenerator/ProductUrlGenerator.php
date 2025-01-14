<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class ProductUrlGenerator implements SubjectUrlGeneratorInterface
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function can(RichSnippetSubjectInterface $subject): bool
    {
        return $subject instanceof ProductInterface;
    }

    public function generateUrl(RichSnippetSubjectInterface $subject): string
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        return $this->router->generate('sylius_shop_product_show', ['slug' => $subject->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}

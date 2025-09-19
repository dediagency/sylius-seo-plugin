<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Factory\SubjectUrl;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\SubjectUrl\SubjectUrlGeneratorInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class TaxonUrlGenerator implements SubjectUrlGeneratorInterface
{
    public function __construct(
        protected readonly RouterInterface $router
    ) {
    }

    public function can(RichSnippetSubjectInterface $subject): bool
    {
        return $subject instanceof TaxonInterface;
    }

    public function generateUrl(RichSnippetSubjectInterface $subject): string
    {
        Assert::isInstanceOf($subject, TaxonInterface::class);

        return $this->router->generate('sylius_shop_product_index', ['slug' => $subject->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}

<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductSubjectFetcher implements SubjectFetcherInterface
{
    private const TYPE = 'product';

    private ChannelContextInterface $channelContext;
    private LocaleContextInterface $localeContext;
    private ProductRepositoryInterface $repository;

    public function __construct(
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ProductRepositoryInterface $repository
    ) {
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->repository = $repository;
    }

    public function can(string $type, ?int $id): bool
    {
        return self::TYPE === $type && null !== $id;
    }

    public function fetch(string $type, ?int $id): ?RichSnippetSubjectInterface
    {
        return $this->repository->find($id);
    }

    public function canFromRequest(Request $request): bool
    {
        return 'sylius_shop_product_show' === $request->attributes->get('_route');
    }

    public function fetchFromRequest(Request $request): ?RichSnippetSubjectInterface
    {
        return $this->repository->findOneByChannelAndSlug(
            $this->channelContext->getChannel(),
            $this->localeContext->getLocaleCode(),
            $request->attributes->get('slug')
        );
    }
}

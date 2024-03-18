<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

class ProductSubjectFetcher implements SubjectFetcherInterface
{
    private ChannelContextInterface $channelContext;

    private LocaleContextInterface $localeContext;

    private ProductRepositoryInterface $repository;

    private FilterInterface $filter;

    public function __construct(
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ProductRepositoryInterface $repository,
        FilterInterface $filter,
    ) {
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->repository = $repository;
        $this->filter = $filter;
    }

    public function fetch(?int $id = null): ?RichSnippetSubjectInterface
    {
        if (null === $id) {
            return null;
        }

        /** @var ?RichSnippetSubjectInterface $richSnippetSubject */
        $richSnippetSubject = $this->repository->find($id);

        return $richSnippetSubject;
    }

    public function supports(Request $request): bool
    {
        return $this->filter->isSatisfiedBy($request);
    }

    public function fetchFromRequest(Request $request): ?RichSnippetSubjectInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $slug = $request->attributes->get('slug');
        if ($slug === null) {
            return null;
        }
        Assert::string($slug);

        /** @var RichSnippetSubjectInterface|null $subject */
        $subject = $this->repository->findOneByChannelAndSlug(
            $channel,
            $this->localeContext->getLocaleCode(),
            $slug,
        );

        return $subject;
    }
}

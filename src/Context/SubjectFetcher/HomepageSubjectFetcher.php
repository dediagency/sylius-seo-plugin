<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\Subject\HomepageRichSnippetSubject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomepageSubjectFetcher implements SubjectFetcherInterface
{
    public const TYPE = 'homepage';

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function can(string $type, ?int $id): bool
    {
        return self::TYPE === $type && null === $id;
    }

    public function fetch(string $type, ?int $id = null): ?RichSnippetSubjectInterface
    {
        return new HomepageRichSnippetSubject($this->translator->trans('sylius.ui.home'));
    }

    public function canFromRequest(Request $request): bool
    {
        return 'sylius_shop_homepage' === $request->attributes->get('_route');
    }

    public function fetchFromRequest(Request $request): ?RichSnippetSubjectInterface
    {
        return $this->fetch(self::TYPE);
    }
}

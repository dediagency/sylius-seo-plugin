<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Twig;

use Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\FilterRegistry;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NoIndexNoFollowExtension extends AbstractExtension
{
    private RequestStack $requestStack;

    private FilterRegistry $filterRegistry;

    public function __construct(
        FilterRegistry $filterRegistry,
        RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;
        $this->filterRegistry = $filterRegistry;
    }

    public function isNoIndexNoFollow(): bool
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return false;
        }

        $filterName = $this->resolveFilterName($request);

        if ('' === $filterName) {
            return false;
        }

        $filter = $this->filterRegistry->getFilter($filterName);

        return $filter->isSatisfiedBy($request);
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dedi_sylius_seo_is_no_index_no_follow', [$this, 'isNoIndexNoFollow']),
        ];
    }

    private function resolveFilterName(Request $request): string
    {
        $seoRouteConfig = $request->attributes->get('_seo', []);

        $filterName = count($seoRouteConfig) === 0 || !array_key_exists('no_index_no_follow_filter', $seoRouteConfig) ? '' : $seoRouteConfig['no_index_no_follow_filter'];
        if (!is_string($filterName)) {
            throw new InvalidArgumentException('Invalid config value provided : _seo.no_index_filter should be of type string');
        }

        return $filterName;
    }
}

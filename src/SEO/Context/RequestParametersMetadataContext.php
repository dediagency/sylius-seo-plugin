<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Context;

use Dedi\SyliusSEOPlugin\Filter\FilterRegistryInterface;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

class RequestParametersMetadataContext implements MetadataContextInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private FilterRegistryInterface $filterRegistry,
    ) {
    }

    public function getMetadata(): Metadata
    {
        $request = $this->requestStack->getCurrentRequest();

        Assert::notNull($request);

        $filterName = $this->resolveFilterName($request);

        Assert::notEmpty($filterName);

        $filter = $this->filterRegistry->getFilter($filterName);

        $isNotIndexable = $filter->isSatisfiedBy($request);

        return new Metadata('request', !$isNotIndexable);
    }

    private function resolveFilterName(Request $request): string
    {
        $seoRouteConfig = (array) $request->attributes->get('_seo', []);

        $filterName = count($seoRouteConfig) === 0 || !array_key_exists('no_index_no_follow_filter', $seoRouteConfig) ? '' : $seoRouteConfig['no_index_no_follow_filter'];
        if (!is_string($filterName)) {
            throw new InvalidArgumentException('Invalid config value provided : _seo.no_index_no_follow_filter should be of type string');
        }

        return $filterName;
    }
}

<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\AbstractRichSnippetFactory;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippet\TaxonRichSnippet;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class TaxonRichSnippetFactory extends AbstractRichSnippetFactory
{
    public function __construct(
        private CacheManager $cacheManager,
        private LocaleContextInterface $localeContext,
        private RouterInterface $router,
    ) {
    }

    /**
     * @param TaxonRichSnippetSubject&TaxonInterface $subject
     */
    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface
    {
        Assert::isInstanceOf($subject, TaxonInterface::class);

        $richSnippet = new TaxonRichSnippet($this->filterNullValues([
            'name' => $subject->getName(),
            'url' => $this->generateTaxonUrl($subject),
            'alternateName' => $subject->getSlug(),
            'inLanguage' => $this->localeContext->getLocaleCode(),
        ]));

        if (null !== $description = $subject->getDescription()) {
            $richSnippet->addData([
                'description' => trim(strip_tags($description)),
            ]);
        }

        $images = $this->buildImages($subject);
        if ([] !== $images) {
            $richSnippet->addData(['image' => $images]);
        }

        if (null !== $parent = $subject->getParent()) {
            $richSnippet->addData(['isPartOf' => $this->buildParentData($parent)]);
        }

        $itemList = $this->buildChildrenItemList($subject);
        if ([] !== $itemList) {
            $richSnippet->addData([
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'itemListOrder' => 'https://schema.org/ItemListOrderAscending',
                    'numberOfItems' => count($itemList),
                    'itemListElement' => $itemList,
                ],
            ]);
        }

        return $richSnippet;
    }

    protected function getHandledSubjectTypes(): array
    {
        return ['taxon'];
    }

    private function buildImages(TaxonInterface $taxon): array
    {
        $images = [];

        foreach ($taxon->getImages() as $image) {
            if (!$image instanceof ImageInterface) {
                continue;
            }

            $path = $image->getPath();
            if (null === $path || '' === $path) {
                continue;
            }

            $images[] = $this->cacheManager->generateUrl($path, 'sylius_shop_taxon_thumbnail');
        }

        return array_values(array_unique($images));
    }

    private function buildParentData(TaxonInterface $parent): array
    {
        return $this->filterNullValues([
            '@type' => 'CollectionPage',
            'name' => $parent->getName(),
            'url' => $this->generateTaxonUrl($parent),
        ]);
    }

    private function buildChildrenItemList(TaxonInterface $taxon): array
    {
        $items = [];
        $position = 1;

        foreach ($taxon->getEnabledChildren() as $child) {
            if (!$child instanceof TaxonInterface) {
                continue;
            }

            $childName = $child->getName();
            if (null === $childName || '' === $childName) {
                continue;
            }

            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'item' => $this->filterNullValues([
                    '@type' => 'CollectionPage',
                    'name' => $childName,
                    'url' => $this->generateTaxonUrl($child),
                ]),
            ];
        }

        return $items;
    }

    private function generateTaxonUrl(TaxonInterface $taxon): ?string
    {
        $slug = $taxon->getSlug();
        if (null === $slug || '' === $slug) {
            return null;
        }

        return $this->router->generate(
            'sylius_shop_product_index',
            ['slug' => $slug],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }

    private function filterNullValues(array $data): array
    {
        return array_filter($data, static fn ($value) => null !== $value && '' !== $value);
    }
}

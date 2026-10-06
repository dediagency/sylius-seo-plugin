<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Factory;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductVariantSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet\ProductGroupRichSnippet;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet\ProductRichSnippet;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet\RichSnippetInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Resolver\RequestedProductVariantResolver;
use Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator\ProductUrlGenerator;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use NumberFormatter;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Model\ProductImagesAwareInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface as BaseProductVariantInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Webmozart\Assert\Assert;

class ProductRichSnippetFactory extends AbstractRichSnippetFactory
{
    public const PRODUCT_AVAILABILITY_DISCONTINUED = 'https://schema.org/Discontinued';

    public const PRODUCT_AVAILABILITY_IN_STOCK = 'https://schema.org/InStock';

    public const PRODUCT_AVAILABILITY_IN_STORE_ONLY = 'https://schema.org/InStoreOnly';

    public const PRODUCT_AVAILABILITY_LIMITED_AVAILABILITY = 'https://schema.org/LimitedAvailability';

    public const PRODUCT_AVAILABILITY_ONLINE_ONLY = 'https://schema.org/OnlineOnly';

    public const PRODUCT_AVAILABILITY_OUT_OF_STOCK = 'https://schema.org/OutOfStock';

    public const PRODUCT_AVAILABILITY_PRE_ORDER = 'https://schema.org/PreOrder';

    public const PRODUCT_AVAILABILITY_PRE_SALE = 'https://schema.org/PreSale';

    public const PRODUCT_AVAILABILITY_SOLD_OUT = 'https://schema.org/SoldOut';

    /** Properties supported by Google for ProductGroup "variesBy" */
    public const VARIES_BY_PROPERTIES = ['color', 'size', 'suggestedAge', 'suggestedGender', 'material', 'pattern'];

    /**
     * @param array<string, string> $variesByMapping Sylius product option code => schema.org property (e.g. ['t_shirt_size' => 'size'])
     */
    public function __construct(
        protected readonly CacheManager $cacheManager,
        protected readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        protected readonly ChannelContextInterface $channelContext,
        protected readonly LocaleContextInterface $localeContext,
        protected readonly CurrencyContextInterface $currencyContext,
        protected readonly ProductUrlGenerator $productUrlGenerator,
        protected readonly AvailabilityCheckerInterface $availabilityChecker,
        protected readonly array $variesByMapping = [],
    ) {
    }

    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface
    {
        Assert::isInstanceOf($subject, ProductInterface::class);
        Assert::isInstanceOf($subject, RichSnippetProductSubjectInterface::class);

        $variants = $this->getEnabledVariants($subject);

        if (count($variants) > 1) {
            return $this->buildProductGroupRichSnippet($subject, $variants);
        }

        $variant = $variants[0] ?? null;
        $identifiers = $this->getIdentifiers($subject, $variant);

        $richSnippet = new ProductRichSnippet($this->filterEmptyValues(array_merge(
            [
                // isbn is only valid on Book, so the product is co-typed
                '@type' => isset($identifiers['isbn']) ? ['Product', 'Book'] : null,
                'name' => $subject->getName(),
                'description' => $subject->getShortDescription(),
                'brand' => $this->getBrand($subject),
            ],
            $identifiers,
            [
                'image' => $this->getImages($subject),
                'offers' => $this->getOffers($subject),
            ],
        )));

        $this->addReviewData($richSnippet, $subject);

        return $richSnippet;
    }

    /**
     * Products with several variants are described as a ProductGroup, each variant being a Product with its own
     * identifiers and offer.
     *
     * @see https://developers.google.com/search/docs/appearance/structured-data/product-variants
     *
     * @param ProductVariantInterface[] $variants
     */
    protected function buildProductGroupRichSnippet(ProductInterface $subject, array $variants): RichSnippetInterface
    {
        Assert::isInstanceOf($subject, RichSnippetProductSubjectInterface::class);

        $url = $this->productUrlGenerator->generateUrl($subject);

        $richSnippet = new ProductGroupRichSnippet($this->filterEmptyValues([
            'name' => $subject->getName(),
            'description' => $subject->getShortDescription(),
            'url' => $url,
            'brand' => $this->getBrand($subject),
            'productGroupID' => $subject->getSEOSku() ?? $subject->getCode(),
            'image' => $this->getImages($subject),
            'variesBy' => $this->getVariesBy($variants),
            'hasVariant' => array_map(
                fn (ProductVariantInterface $variant): array => $this->buildVariant($subject, $variant, $url),
                $variants,
            ),
        ]));

        $this->addReviewData($richSnippet, $subject);

        return $richSnippet;
    }

    protected function buildVariant(ProductInterface $product, ProductVariantInterface $variant, string $url): array
    {
        $variantImages = $this->getImages($variant);

        $identifiers = $this->getIdentifiers(null, $variant);
        // Google requires a unique identifier per variant: fall back on the Sylius variant code
        if (!isset($identifiers['sku']) && null !== $variant->getCode() && '' !== $variant->getCode()) {
            $identifiers['sku'] = $variant->getCode();
        }

        return $this->filterEmptyValues(array_merge(
            [
                '@type' => 'Product',
                'name' => $this->getVariantName($product, $variant),
                'image' => [] !== $variantImages ? $variantImages : $this->getImages($product),
            ],
            $identifiers,
            $this->getVariantProperties($variant),
            [
                'offers' => $this->buildOffer($variant, $this->getVariantUrl($url, $variant)),
            ],
        ));
    }

    /**
     * URL preselecting the variant on the product page (see RequestedProductVariantResolver).
     */
    protected function getVariantUrl(string $productUrl, ProductVariantInterface $variant): string
    {
        if (null === $variant->getCode()) {
            return $productUrl;
        }

        return $productUrl
            . (str_contains($productUrl, '?') ? '&' : '?')
            . http_build_query([RequestedProductVariantResolver::QUERY_PARAMETER => $variant->getCode()]);
    }

    /**
     * Product name followed by the variant name, or by its option values when it has no name (e.g. "T-Shirt - XL").
     */
    protected function getVariantName(ProductInterface $product, ProductVariantInterface $variant): ?string
    {
        $variantName = $variant->getName();

        if (null === $variantName || '' === $variantName) {
            $variantName = implode(' / ', array_filter(array_map(
                static fn (ProductOptionValueInterface $optionValue): ?string => $optionValue->getValue(),
                $variant->getOptionValues()->toArray(),
            )));
        }

        if ('' === $variantName) {
            return $product->getName();
        }

        return sprintf('%s - %s', $product->getName(), $variantName);
    }

    /**
     * Option values of the variant, for the options mapped to a property supported by Google (e.g. "size": "XL").
     */
    protected function getVariantProperties(ProductVariantInterface $variant): array
    {
        $properties = [];

        foreach ($variant->getOptionValues() as $optionValue) {
            $property = $this->variesByMapping[(string) $optionValue->getOptionCode()] ?? null;

            if (in_array($property, self::VARIES_BY_PROPERTIES, true)) {
                $properties[$property] = $optionValue->getValue();
            }
        }

        return $properties;
    }

    /**
     * @param ProductVariantInterface[] $variants
     *
     * @return string[]
     */
    protected function getVariesBy(array $variants): array
    {
        $variesBy = [];

        foreach ($variants as $variant) {
            foreach (array_keys($this->getVariantProperties($variant)) as $property) {
                $variesBy[$property] = 'https://schema.org/' . $property;
            }
        }

        return array_values($variesBy);
    }

    /**
     * Identifiers of the variant, falling back on the product ones when the product is given.
     */
    protected function getIdentifiers(?RichSnippetProductSubjectInterface $product, ?BaseProductVariantInterface $variant): array
    {
        $variantIdentifiers = $variant instanceof RichSnippetProductVariantSubjectInterface ? [
            'gtin8' => $variant->getSEOGtin8(),
            'gtin13' => $variant->getSEOGtin13(),
            'gtin14' => $variant->getSEOGtin14(),
            'mpn' => $variant->getSEOMpn(),
            'sku' => $variant->getSEOSku(),
        ] : [];

        $productIdentifiers = null !== $product ? [
            'gtin8' => $product->getSEOGtin8(),
            'gtin13' => $product->getSEOGtin13(),
            'gtin14' => $product->getSEOGtin14(),
            'mpn' => $product->getSEOMpn(),
            'isbn' => $product->getSEOIsbn(),
            'sku' => $product->getSEOSku(),
        ] : [];

        $identifiers = [];
        foreach (['gtin8', 'gtin13', 'gtin14', 'mpn', 'isbn', 'sku'] as $key) {
            $value = $variantIdentifiers[$key] ?? null;
            if (null === $value || '' === $value) {
                $value = $productIdentifiers[$key] ?? null;
            }
            $identifiers[$key] = $value;
        }

        return $this->filterEmptyValues($identifiers);
    }

    protected function getBrand(RichSnippetProductSubjectInterface $subject): ?array
    {
        if (null === $subject->getSEOBrand() || '' === $subject->getSEOBrand()) {
            return null;
        }

        return [
            '@type' => 'Brand',
            'name' => $subject->getSEOBrand(),
        ];
    }

    /**
     * @return string[]
     */
    protected function getImages(ImagesAwareInterface|ProductImagesAwareInterface $subject): array
    {
        return array_values(array_filter(array_map(
            function (ImageInterface $image): ?string {
                if (null === $image->getPath()) {
                    return null;
                }

                return $this->cacheManager->generateUrl($image->getPath(), 'sylius_shop_product_large_thumbnail');
            },
            $subject->getImages()->toArray(),
        )));
    }

    protected function addReviewData(RichSnippetInterface $richSnippet, ProductInterface $subject): void
    {
        if ($subject->getAcceptedReviews()->count() > 0) {
            ['review' => $review, 'aggregateRating' => $aggregateRating] = $this->getReviewAndRatingData($subject);

            $richSnippet->addData([
                'review' => $review,
                'aggregateRating' => $aggregateRating,
            ]);
        }
    }

    protected function getOffers(RichSnippetSubjectInterface $subject): array
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        $url = $this->productUrlGenerator->generateUrl($subject);

        return array_map(
            fn (ProductVariantInterface $variant): array => $this->buildOffer($variant, $url),
            $this->getEnabledVariants($subject),
        );
    }

    protected function buildOffer(ProductVariantInterface $variant, string $url): array
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $currencyCode = $this->currencyContext->getCurrencyCode();

        $price = $this->productVariantPricesCalculator->calculate(
            $variant,
            ['channel' => $channel],
        );

        return [
            '@type' => 'Offer',
            'url' => $url,
            'priceCurrency' => $currencyCode,
            'price' => $this->formatCurrencyForRichSnippets($price, $currencyCode),
            'availability' => $this->getAvailability($variant),
        ];
    }

    /**
     * @return ProductVariantInterface[]
     */
    protected function getEnabledVariants(ProductInterface $subject): array
    {
        $variants = array_values(array_filter(
            $subject->getVariants()->toArray(),
            static fn (BaseProductVariantInterface $variant): bool => $variant->isEnabled(),
        ));
        Assert::allIsInstanceOf($variants, ProductVariantInterface::class);

        return $variants;
    }

    protected function filterEmptyValues(array $data): array
    {
        return array_filter(
            $data,
            static fn (mixed $value): bool => null !== $value && '' !== $value && [] !== $value,
        );
    }

    private function getAvailability(StockableInterface $stockable): string
    {
        if (!$stockable->isTracked()) {
            return self::PRODUCT_AVAILABILITY_IN_STOCK;
        }

        return $this->availabilityChecker->isStockAvailable($stockable) ? self::PRODUCT_AVAILABILITY_IN_STOCK : self::PRODUCT_AVAILABILITY_OUT_OF_STOCK;
    }

    protected function formatCurrencyForRichSnippets(int $amount, string $currency): string
    {
        $formatter = new NumberFormatter($this->localeContext->getLocaleCode(), NumberFormatter::CURRENCY);

        // let's remove any monetary symbol and spaces
        $formatter->setSymbol(NumberFormatter::MONETARY_SEPARATOR_SYMBOL, '.');
        $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, '.');
        $formatter->setPattern('#0.0#');

        $result = $formatter->formatCurrency(abs($amount / 100), $currency);

        return $amount >= 0 ? $result : '-' . $result;
    }

    protected function getReviewAndRatingData(ProductInterface $subject): array
    {
        /** @var ReviewInterface $bestReview */
        $bestReview = $subject->getAcceptedReviews()->first();
        foreach ($subject->getAcceptedReviews() as $review) {
            if ($bestReview->getRating() < $review->getRating()) {
                $bestReview = $review;
            }
        }

        $reviewData = [
            '@type' => 'Review',
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $bestReview->getRating(),
            ],
            'reviewBody' => $bestReview->getComment(),
        ];

        if ((null !== $author = $bestReview->getAuthor()) && (null !== $author->getFirstName() || null !== $author->getLastName())) {
            $reviewData['author'] = [
                '@type' => 'Person',
                'name' => trim(sprintf( // either firstName, lastName or both
                    '%s %s',
                    $author->getFirstName(),
                    $author->getLastName(),
                )),
            ];
        }

        return [
            'review' => $reviewData,
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => $subject->getAverageRating(),
                'reviewCount' => $subject->getAcceptedReviews()->count(),
            ],
        ];
    }

    protected function getHandledSubjectTypes(): array
    {
        return ['product'];
    }
}

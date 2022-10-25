<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetProductSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\AbstractRichSnippetFactory;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippet\ProductRichSnippet;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;
use Dedi\SyliusSEOPlugin\Factory\SubjectUrl\ProductUrlGenerator;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use NumberFormatter;
use Sylius\Bundle\CoreBundle\Templating\Helper\PriceHelper;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
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

    protected CacheManager $cacheManager;

    protected PriceHelper $priceHelper;

    protected ChannelContextInterface $channelContext;

    protected LocaleContextInterface $localeContext;

    protected CurrencyContextInterface $currencyContext;

    protected ProductUrlGenerator $productUrlGenerator;

    protected AvailabilityCheckerInterface $availabilityChecker;

    public function __construct(
        CacheManager $cacheManager,
        PriceHelper $priceHelper,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext,
        ProductUrlGenerator $productUrlGenerator,
        AvailabilityCheckerInterface $availabilityChecker
    ) {
        $this->cacheManager = $cacheManager;
        $this->priceHelper = $priceHelper;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->currencyContext = $currencyContext;
        $this->productUrlGenerator = $productUrlGenerator;
        $this->availabilityChecker = $availabilityChecker;
    }

    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface
    {
        Assert::isInstanceOf($subject, ProductInterface::class);
        Assert::isInstanceOf($subject, RichSnippetProductSubjectInterface::class);

        $richSnippet = new ProductRichSnippet([
            'name' => $subject->getName(),
            'description' => $subject->getShortDescription(),
        ]);

        if (null !== $subject->getSEOBrand()) {
            $richSnippet->addData([
                'brand' => [
                    'type' => 'Thing',
                    'name' => $subject->getSEOBrand(),
                ],
            ]);
        }

        if (null !== $subject->getSEOGtin8()) {
            $richSnippet->addData([
                'gtin8' => $subject->getSEOGtin8(),
            ]);
        }
        if (null !== $subject->getSEOGtin13()) {
            $richSnippet->addData([
                'gtin13' => $subject->getSEOGtin13(),
            ]);
        }

        if (null !== $subject->getSEOGtin14()) {
            $richSnippet->addData([
                'gtin14' => $subject->getSEOGtin14(),
            ]);
        }

        if (null !== $subject->getSEOMpn()) {
            $richSnippet->addData([
                'mpn' => $subject->getSEOMpn(),
            ]);
        }

        if (null !== $subject->getSEOIsbn()) {
            $richSnippet->addData([
                'isbn' => $subject->getSEOIsbn(),
            ]);
        }

        if (null !== $subject->getSEOSku()) {
            $richSnippet->addData([
                'sku' => $subject->getSEOSku(),
            ]);
        }

        if ($subject->getImages()->count() > 0) {
            $richSnippet->addData([
                'image' => array_map(fn (ImageInterface $image) => $this->cacheManager->generateUrl($image->getPath(), 'sylius_shop_product_large_thumbnail'), $subject->getImages()->toArray()),
            ]);
        }

        $richSnippet->addData([
            'offers' => $this->getOffers($subject),
        ]);

        if ($subject->getAcceptedReviews()->count() > 0) {
            ['review' => $review, 'aggregateRating' => $aggregateRating] = $this->getReviewAndRatingData($subject);

            $richSnippet->addData([
                'review' => $review,
                'aggregateRating' => $aggregateRating,
            ]);
        }

        return $richSnippet;
    }

    protected function getOffers(RichSnippetSubjectInterface $subject): array
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $url = $this->productUrlGenerator->generateUrl($subject);
        $currencyCode = $this->currencyContext->getCurrencyCode();

        return array_map(function (ProductVariantInterface $variant) use ($channel, $url, $currencyCode) {
            $price = $this->priceHelper->getPrice(
                $variant,
                ['channel' => $channel]
            );

            return [
                '@type' => 'Offer',
                'url' => $url,
                'priceCurrency' => $currencyCode,
                'price' => $this->formatCurrencyForRichSnippets($price, $currencyCode),
                'availability' => $this->getAvailability($variant),
            ];
        }, $subject->getVariants()->toArray());
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
                    $author->getLastName()
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

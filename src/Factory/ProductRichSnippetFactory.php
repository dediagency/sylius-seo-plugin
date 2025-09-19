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
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
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

    public function __construct(
        protected readonly CacheManager $cacheManager,
        protected readonly ProductVariantPricesCalculatorInterface $productVariantPriceCalculator,
        protected readonly ChannelContextInterface $channelContext,
        protected readonly LocaleContextInterface $localeContext,
        protected readonly CurrencyContextInterface $currencyContext,
        protected readonly ProductUrlGenerator $productUrlGenerator,
        protected readonly AvailabilityCheckerInterface $availabilityChecker,
    ) {
    }

    /**
     * @param ProductRichSnippetSubject $subject
     */
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
                    'type' => 'Brand',
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
            if (!$variant->isEnabled()) {
                return;
            }

            $price = $this->productVariantPriceCalculator->calculate(
                $variant,
                ['channel' => $channel],
            );

            return [
                '@type' => 'Offer',
                'url' => $url,
                'priceCurrency' => $currencyCode,
                'price' => $this->formatCurrencyForRichSnippets($price, $currencyCode),
                'availability' => $this->getAvailability($variant),
                'priceValidUntil' => (new \DateTime())->modify('+1 month')->format('Y-m-d'),
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
        $formatter = new \NumberFormatter($this->localeContext->getLocaleCode(), \NumberFormatter::CURRENCY);

        // let's remove any monetary symbol and spaces
        $formatter->setSymbol(\NumberFormatter::MONETARY_SEPARATOR_SYMBOL, '.');
        $formatter->setSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, '.');
        $formatter->setPattern('#0.0#');

        $result = $formatter->formatCurrency(abs($amount / 100), $currency);

        return $amount >= 0 ? $result : '-'.$result;
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
            'author' => [
                '@type' => 'Person',
                'name' => !empty($bestReview->getAuthor()->getFullName()) ? $bestReview->getAuthor()->getFullName() : $bestReview->getAuthor()->getEmail(),
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

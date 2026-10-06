<?php

declare(strict_types=1);

namespace spec\Dedi\SyliusSEOPlugin\RichSnippet\Factory;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductVariantSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Factory\ProductRichSnippetFactory;
use Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator\ProductUrlGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

class ProductRichSnippetFactorySpec extends ObjectBehavior
{
    function let(
        CacheManager $cacheManager,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext,
        ProductUrlGenerator $productUrlGenerator,
        AvailabilityCheckerInterface $availabilityChecker,
    ) {
        $this->beConstructedWith(
            $cacheManager,
            $productVariantPricesCalculator,
            $channelContext,
            $localeContext,
            $currencyContext,
            $productUrlGenerator,
            $availabilityChecker,
            ['plant_size' => 'size', 'plant_pot' => 'pot'],
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductRichSnippetFactory::class);
    }

    function it_can_handle_valid_subject(RichSnippetSubjectInterface $subject)
    {
        $subject->getRichSnippetSubjectType()->willReturn('product');

        $this->can($subject)->shouldReturn(true);
    }

    function it_can_t_handle_invalid_subject(RichSnippetSubjectInterface $subject)
    {
        $subject->getRichSnippetSubjectType()->willReturn('unknown_subject_type');

        $this->can($subject)->shouldReturn(false);
    }

    function it_builds_a_product_rich_snippet_for_a_product_with_a_single_enabled_variant(
        CacheManager $cacheManager,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext,
        ChannelInterface $channel,
        ProductUrlGenerator $productUrlGenerator,
    ) {
        $subject = \Mockery::mock(ProductInterface::class, RichSnippetProductSubjectInterface::class);

        $image = $this->mockImage('image/a/path');
        $cacheManager->generateUrl('image/a/path', 'sylius_shop_product_large_thumbnail')->willReturn('/my_shop/images/large-thumbnail/image-a');

        $channelContext->getChannel()->willReturn($channel);
        $currencyContext->getCurrencyCode()->willReturn('EUR');
        $localeContext->getLocaleCode()->willReturn('fr_FR');

        $enabledVariant = \Mockery::mock(ProductVariantInterface::class, RichSnippetProductVariantSubjectInterface::class);
        $enabledVariant->shouldReceive([
            'isEnabled' => true,
            'isTracked' => false,
            'getSEOGtin8' => null,
            'getSEOGtin13' => 'variant gtin13',
            'getSEOGtin14' => '',
            'getSEOMpn' => null,
            'getSEOSku' => 'variant sku',
        ]);
        $disabledVariant = \Mockery::mock(ProductVariantInterface::class);
        $disabledVariant->shouldReceive(['isEnabled' => false]);

        $productUrlGenerator->generateUrl($subject)->willReturn('/my_shop/products/ficus');
        $productVariantPricesCalculator->calculate($enabledVariant, ['channel' => $channel])->willReturn(69009);

        $subject->shouldReceive([
            'getName' => 'Ficus',
            'getShortDescription' => null,
            'getSEOBrand' => 'Dedi',
            'getSEOGtin8' => 'my gtin8',
            'getSEOGtin13' => 'my gtin13',
            'getSEOGtin14' => null,
            'getSEOMpn' => 'my mpn',
            'getSEOIsbn' => 'my isbn',
            'getSEOSku' => 'my sku',
            'getImages' => new ArrayCollection([$image]),
            'getAcceptedReviews' => new ArrayCollection(),
            'getVariants' => new ArrayCollection([$enabledVariant, $disabledVariant]),
        ]);

        $richSnippet = $this->buildRichSnippet($subject);

        $richSnippet->getType()->shouldReturn('product');
        $richSnippet->getData()->shouldReturn([
            [
                '@context' => 'https://schema.org',
                '@type' => ['Product', 'Book'],
                'name' => 'Ficus',
                'brand' => [
                    '@type' => 'Brand',
                    'name' => 'Dedi',
                ],
                'gtin8' => 'my gtin8',
                'gtin13' => 'variant gtin13',
                'mpn' => 'my mpn',
                'isbn' => 'my isbn',
                'sku' => 'variant sku',
                'image' => ['/my_shop/images/large-thumbnail/image-a'],
                'offers' => [
                    [
                        '@type' => 'Offer',
                        'url' => '/my_shop/products/ficus',
                        'priceCurrency' => 'EUR',
                        'price' => '690.09',
                        'availability' => 'https://schema.org/InStock',
                    ],
                ],
            ],
        ]);
    }

    function it_builds_a_product_group_rich_snippet_for_a_product_with_several_enabled_variants(
        CacheManager $cacheManager,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext,
        ChannelInterface $channel,
        ProductUrlGenerator $productUrlGenerator,
        AvailabilityCheckerInterface $availabilityChecker,
    ) {
        $subject = \Mockery::mock(ProductInterface::class, RichSnippetProductSubjectInterface::class);

        $imageA = $this->mockImage('image/a/path');
        $imageB = $this->mockImage('image/b/path');
        $variantImage = $this->mockImage('image/variant/path');

        $cacheManager->generateUrl('image/a/path', 'sylius_shop_product_large_thumbnail')->willReturn('/my_shop/images/large-thumbnail/image-a');
        $cacheManager->generateUrl('image/b/path', 'sylius_shop_product_large_thumbnail')->willReturn('/my_shop/images/large-thumbnail/image-b');
        $cacheManager->generateUrl('image/variant/path', 'sylius_shop_product_large_thumbnail')->willReturn('/my_shop/images/large-thumbnail/image-variant');

        $channelContext->getChannel()->willReturn($channel);
        $currencyContext->getCurrencyCode()->willReturn('EUR');
        $localeContext->getLocaleCode()->willReturn('fr_FR');

        $variantA = \Mockery::mock(ProductVariantInterface::class, RichSnippetProductVariantSubjectInterface::class);
        $variantA->shouldReceive([
            'isEnabled' => true,
            'isTracked' => false,
            'getCode' => 'FICUS_SMALL',
            'getName' => null,
            'getOptionValues' => new ArrayCollection([
                $this->mockOptionValue('plant_size', 'Small'),
                $this->mockOptionValue('plant_pot', 'Terracotta'),
            ]),
            'getImages' => new ArrayCollection([$variantImage]),
            'getSEOGtin8' => null,
            'getSEOGtin13' => '1234567890123',
            'getSEOGtin14' => null,
            'getSEOMpn' => 'variant a mpn',
            'getSEOSku' => 'FICUS-S',
        ]);
        $variantB = \Mockery::mock(ProductVariantInterface::class);
        $variantB->shouldReceive([
            'isEnabled' => true,
            'isTracked' => true,
            'getCode' => 'FICUS_LARGE',
            'getName' => 'Large',
            'getOptionValues' => new ArrayCollection([
                $this->mockOptionValue('plant_size', 'Large'),
            ]),
            'getImages' => new ArrayCollection(),
        ]);
        $disabledVariant = \Mockery::mock(ProductVariantInterface::class);
        $disabledVariant->shouldReceive(['isEnabled' => false]);

        $availabilityChecker->isStockAvailable($variantB)->willReturn(false);

        $productUrlGenerator->generateUrl($subject)->willReturn('/my_shop/products/ficus');

        $productVariantPricesCalculator->calculate($variantA, ['channel' => $channel])->willReturn(1337);
        $productVariantPricesCalculator->calculate($variantB, ['channel' => $channel])->willReturn(12345);

        $reviewA = \Mockery::mock(ReviewInterface::class);
        $reviewB = \Mockery::mock(ReviewInterface::class);
        $reviewC = \Mockery::mock(ReviewInterface::class);

        $author = \Mockery::mock(ReviewerInterface::class);

        $reviewA->shouldReceive([
            'getRating' => 2,
        ]);
        $reviewB->shouldReceive([
            'getRating' => 5,
            'getComment' => 'This is a gr8 plant',
            'getAuthor' => $author,
        ]);
        $reviewC->shouldReceive([
            'getRating' => 4,
        ]);

        $author->shouldReceive([
            'getFirstName' => 'Capucine',
            'getLastName' => null,
        ]);

        $subject->shouldReceive([
            'getName' => 'Ficus',
            'getCode' => 'FICUS',
            'getShortDescription' => 'Such a nice plant :)',
            'getSEOBrand' => 'Dedi',
            'getSEOGtin8' => 'my gtin8',
            'getSEOGtin13' => 'my gtin13',
            'getSEOGtin14' => 'my gtin14',
            'getSEOMpn' => 'my mpn',
            'getSEOIsbn' => 'my isbn',
            'getSEOSku' => null,
            'getImages' => new ArrayCollection([$imageA, $imageB]),
            'getAcceptedReviews' => new ArrayCollection([$reviewA, $reviewB, $reviewC]),
            'getVariants' => new ArrayCollection([$variantA, $disabledVariant, $variantB]),
            'getAverageRating' => 4.6667,
        ]);

        $richSnippet = $this->buildRichSnippet($subject);

        $richSnippet->getType()->shouldReturn('product_group');
        $richSnippet->getData()->shouldReturn([
            [
                '@context' => 'https://schema.org',
                '@type' => 'ProductGroup',
                'name' => 'Ficus',
                'description' => 'Such a nice plant :)',
                'url' => '/my_shop/products/ficus',
                'brand' => [
                    '@type' => 'Brand',
                    'name' => 'Dedi',
                ],
                'productGroupID' => 'FICUS',
                'image' => [
                    '/my_shop/images/large-thumbnail/image-a',
                    '/my_shop/images/large-thumbnail/image-b',
                ],
                'variesBy' => ['https://schema.org/size'],
                'hasVariant' => [
                    [
                        '@type' => 'Product',
                        'name' => 'Ficus - Small / Terracotta',
                        'image' => ['/my_shop/images/large-thumbnail/image-variant'],
                        'gtin13' => '1234567890123',
                        'mpn' => 'variant a mpn',
                        'sku' => 'FICUS-S',
                        'size' => 'Small',
                        'offers' => [
                            '@type' => 'Offer',
                            'url' => '/my_shop/products/ficus?variant=FICUS_SMALL',
                            'priceCurrency' => 'EUR',
                            'price' => '13.37',
                            'availability' => 'https://schema.org/InStock',
                        ],
                    ],
                    [
                        '@type' => 'Product',
                        'name' => 'Ficus - Large',
                        'image' => [
                            '/my_shop/images/large-thumbnail/image-a',
                            '/my_shop/images/large-thumbnail/image-b',
                        ],
                        'size' => 'Large',
                        'offers' => [
                            '@type' => 'Offer',
                            'url' => '/my_shop/products/ficus?variant=FICUS_LARGE',
                            'priceCurrency' => 'EUR',
                            'price' => '123.45',
                            'availability' => 'https://schema.org/OutOfStock',
                        ],
                    ],
                ],
                'review' => [
                    '@type' => 'Review',
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => 5,
                    ],
                    'reviewBody' => 'This is a gr8 plant',
                    'author' => [
                        '@type' => 'Person',
                        'name' => 'Capucine',
                    ],
                ],
                'aggregateRating' => [
                    '@type' => 'AggregateRating',
                    'ratingValue' => 4.6667,
                    'reviewCount' => 3,
                ],
            ],
        ]);
    }

    private function mockImage(string $path): ImageInterface
    {
        $image = \Mockery::mock(ImageInterface::class);
        $image->shouldReceive(['getPath' => $path]);

        return $image;
    }

    private function mockOptionValue(string $optionCode, string $value): ProductOptionValueInterface
    {
        $optionValue = \Mockery::mock(ProductOptionValueInterface::class);
        $optionValue->shouldReceive([
            'getOptionCode' => $optionCode,
            'getValue' => $value,
        ]);

        return $optionValue;
    }
}

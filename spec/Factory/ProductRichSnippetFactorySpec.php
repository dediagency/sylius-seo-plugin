<?php

namespace spec\Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Factory\ProductRichSnippetFactory;
use Dedi\SyliusSEOPlugin\Factory\SubjectUrl\ProductUrlGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Templating\Helper\PriceHelper;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use function Clue\StreamFilter\fun;

class ProductRichSnippetFactorySpec extends ObjectBehavior
{
    function let(
        CacheManager $cacheManager,
        PriceHelper $priceHelper,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext,
        ProductUrlGenerator $productUrlGenerator,
        AvailabilityCheckerInterface $availabilityChecker
    ) {
        $this->beConstructedWith(
            $cacheManager,
            $priceHelper,
            $channelContext,
            $localeContext,
            $currencyContext,
            $productUrlGenerator,
            $availabilityChecker
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

    function it_builds_list_rich_snippets(
        CacheManager $cacheManager,
        PriceHelper $priceHelper,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext,
        ChannelInterface $channel,
        ProductUrlGenerator $productUrlGenerator,
        AvailabilityCheckerInterface $availabilityChecker
    ) {
        $subject = \Mockery::mock(ProductInterface::class, RichSnippetSubjectInterface::class);

        $imageA = \Mockery::mock(ImageInterface::class);
        $imageA->shouldReceive([
            'getPath' => 'image/a/path',
        ]);
        $imageB = \Mockery::mock(ImageInterface::class);
        $imageB->shouldReceive([
            'getPath' => 'image/b/path',
        ]);

        $images = new ArrayCollection([$imageA, $imageB]);

        $cacheManager->generateUrl('image/a/path', 'sylius_shop_product_large_thumbnail')->willReturn('/my_shop/images/large-thumbnail/image-a');
        $cacheManager->generateUrl('image/b/path', 'sylius_shop_product_large_thumbnail')->willReturn('/my_shop/images/large-thumbnail/image-b');

        $channelContext->getChannel()->willReturn($channel);

        $variantA = \Mockery::mock(ProductVariantInterface::class);
        $variantA->shouldReceive([
            'isTracked' => false,
        ]);
        $variantB = \Mockery::mock(ProductVariantInterface::class);
        $variantB->shouldReceive([
            'isTracked' => true,
        ]);
        $variantC = \Mockery::mock(ProductVariantInterface::class);
        $variantC->shouldReceive([
            'isTracked' => true,
        ]);

        $availabilityChecker->isStockAvailable(Argument::any())->willReturn(
            false,
            true
        );

        $variants = new ArrayCollection([$variantA, $variantB, $variantC]);

        $productUrlGenerator->generateUrl($subject)->willReturn('/my_shop/products/ficus');

        $priceHelper->getPrice(Argument::any(), ['channel' => $channel])->willReturn(
            69009,
            1337,
            12345
        );

        $currencyContext->getCurrencyCode()->willReturn('EUR');

        $localeContext->getLocaleCode()->willReturn('fr_FR');

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

        $reviews = new ArrayCollection([$reviewA, $reviewB, $reviewC]);

        $subject->shouldReceive([
            'getName' => 'Ficus',
            'getShortDescription' => 'Such a nice plant :)',
            'getSEOBrand' => 'Dedi',
            'getSEOGtin8' => 'my gtin8',
            'getSEOGtin13' => 'my gtin13',
            'getSEOGtin14' => 'my gtin14',
            'getSEOMpn' => 'my mpn',
            'getSEOIsbn' => 'my isbn',
            'getSEOSku' => 'my sku',
            'getImages' => $images,
            'getAcceptedReviews' => $reviews,
            'getVariants' => $variants,
            'getAverageRating' => 4.6667,
        ]);

        $richSnippet = $this->buildRichSnippet($subject);

        $richSnippet->getData()->shouldReturn([
            [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => 'Ficus',
                'description' => 'Such a nice plant :)',
                'brand' => [
                    '@type' => 'Brand',
                    'name' => 'Dedi',
                ],
                'gtin8' => 'my gtin8',
                'gtin13' => 'my gtin13',
                'gtin14' => 'my gtin14',
                'mpn' => 'my mpn',
                'isbn' => 'my isbn',
                'sku' => 'my sku',
                'image' => [
                    0 => '/my_shop/images/large-thumbnail/image-a',
                    1 => '/my_shop/images/large-thumbnail/image-b',
                ],
                'offers' => [
                    [
                        '@type' => 'Offer',
                        'url' => "/my_shop/products/ficus",
                        'priceCurrency' => 'EUR',
                        'price' => '690,09',
                        'availability' => 'https://schema.org/InStock',
                    ],
                    [
                        '@type' => 'Offer',
                        'url' => "/my_shop/products/ficus",
                        'priceCurrency' => 'EUR',
                        'price' => '13,37',
                        'availability' => 'https://schema.org/OutOfStock',
                    ],
                    [
                        '@type' => 'Offer',
                        'url' => "/my_shop/products/ficus",
                        'priceCurrency' => 'EUR',
                        'price' => '123,45',
                        'availability' => 'https://schema.org/InStock',
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
                        'name' => 'Capucine'
                    ]
                ],
                'aggregateRating' => [
                    '@type' => 'AggregateRating',
                    'ratingValue' => 4.6667,
                    'reviewCount' => 3,
                ],
            ]
        ]);
    }
}

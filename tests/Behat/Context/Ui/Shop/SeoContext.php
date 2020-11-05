<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop\RichSnippetAwarePageInterface;
use Webmozart\Assert\Assert;

class SeoContext implements Context
{
    public const HOMEPAGE = 'home';
    public const CONTACT_PAGE = 'contact';
    public const TAXON_PAGE = 'taxon';
    public const PRODUCT_PAGE = 'product';

    public const RICHSNIPPET_BREADCRUMB = 'BreadcrumbList';
    public const RICHSNIPPET_PRODUCT = 'Product';

    /** @var RichSnippetAwarePageInterface[] */
    private array $pages;

    private RichSnippetAwarePageInterface $currentPage;

    private ProductRepositoryInterface $productRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(
        RichSnippetAwarePageInterface $homePage,
        RichSnippetAwarePageInterface $contactPage,
        RichSnippetAwarePageInterface $taxonPage,
        RichSnippetAwarePageInterface $productPage,
        ProductRepositoryInterface $productRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->pages = [
            self::HOMEPAGE => $homePage,
            self::CONTACT_PAGE => $contactPage,
            self::TAXON_PAGE => $taxonPage,
            self::PRODUCT_PAGE => $productPage,
        ];

        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @BeforeScenario
     * Clean product 727F_patched_cropped_jeans fixture to delete random values
     */
    public function setupFeature()
    {
        $product = $this->productRepository->findOneByCode('727F_patched_cropped_jeans');
        $product->setShortDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
        $product->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');

        /** @var ProductImageInterface $image */
        $image = $product->getImages()->first();
        $image->setPath('727F_patched_cropped_jeans.jpeg');

        /** @var ProductVariantInterface $variant */
        foreach ($product->getVariants() as $index => $variant) {
            /** @var ChannelPricingInterface $channelPricing */
            foreach ($variant->getChannelPricings() as $channelPricing) {
                $channelPricing->setPrice(1000 * ($index + 1));
            }
        }

        foreach ($product->getReviews() as $review) {
            $this->entityManager->remove($review);
            $this->entityManager->flush();
        }

        $this->entityManager->persist($product);
        $this->entityManager->persist($image);
        $this->entityManager->flush();
    }

    /**
     * @When a Googlebot visits the :page page
     * @When a Googlebot visits the :page page with the following parameters:
     */
    public function googlebotVisitsThePage(string $page, ?TableNode $table = null): void
    {
        $this->currentPage = $this->getPage($page);

        $params = [];
        if ($table) {
            $params = $table->getHash()[0];
        }

        $this->currentPage->open($params);
    }

    /**
     * @Then it should access the following breadcrumb:
     */
    public function itShouldAccessTheBreadcrumb(TableNode $table): void
    {
        $richSnippets = $this->currentPage->getRichSnippetData();

        Assert::keyExists(
            $richSnippets,
            self::RICHSNIPPET_BREADCRUMB,
            'This page doesn\'t have the Breadcrumb Rich Snippet'
        );

        $expectedItemListElement = [];
        foreach ($table->getHash() as $k => $item) {
            $itemData = [
                '@type' => 'ListItem',
                'position' => $k + 1,
                'name' => $item['name'],
            ];

            if (array_key_exists('url', $item) && !empty($item['url'])) {
                $itemData['item'] = $item['url'];
            }

            $expectedItemListElement[] = $itemData;
        }

        $expected = [
            [
                '@context' => 'https://schema.org',
                '@type' => self::RICHSNIPPET_BREADCRUMB,
                'itemListElement' => $expectedItemListElement,
            ],
        ];

        Assert::same(
            $richSnippets[self::RICHSNIPPET_BREADCRUMB],
            $expected,
            'Expected breadcrumb Rich Snippet and resolved breadcrumb Rich Snippet do not match'
        );
    }

    /**
     * @Then /^it should access the product named "([^"]*)" with the description "([^"]*)", the image "([^"]*)", the currency "([^"]*)" and the following offers:$/
     */
    public function itShouldAccessTheProduct(string $name, string $description, string $image, string $currency, TableNode $table): void
    {
        $richSnippets = $this->currentPage->getRichSnippetData();

        Assert::keyExists(
            $richSnippets,
            self::RICHSNIPPET_PRODUCT,
            'This page doesn\'t have the Product Rich Snippet'
        );

        $expected = [
            [
                '@context' => 'https://schema.org',
                '@type' => self::RICHSNIPPET_PRODUCT,
                'name' => $name,
                'description' => $description,
                'image' => [$image],
                'offers' => array_map(function ($offer) use ($currency) {
                    return [
                        '@type' => 'Offer',
                        'url' => $this->currentPage->getCurrentUrl(),
                        'priceCurrency' => $currency,
                        'price' => $offer['price'],
                        'availability' => $offer['isInStock'] ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    ];
                }, $table->getHash()),
            ],
        ];

        Assert::same(
            $richSnippets[self::RICHSNIPPET_PRODUCT],
            $expected,
            'Expected product Rich Snippet and resolved product Rich Snippet do not match'
        );
    }

    /**
     * @Then /^it should have the following og data:$/
     */
    public function itShouldHaveTheOgTitleAndTheOgUrl(TableNode $table)
    {
        $data = $this->currentPage->getOgData();

        foreach ($table->getHash() as $ogDatum) {
            Assert::keyExists(
                $data,
                sprintf('og:%s', $ogDatum['name']),
                sprintf('This page doesn\'t have an og:%s meta tag', $ogDatum['name'])
            );

            if (preg_match('/\%([a-z]+)\%/', $ogDatum['data'], $matches)) {
                $type = $matches[1];
                Assert::$type($ogDatum['data']);
            } else {
                Assert::same(
                    $data[sprintf('og:%s', $ogDatum['name'])],
                    $ogDatum['data'],
                    sprintf('Invalid og:%s, expected "%s", got "%s"', $ogDatum['name'], $ogDatum['data'], $data[sprintf('og:%s', $ogDatum['name'])])
                );
            }
        }
    }

    /**
     * @param string $code
     * @return RichSnippetAwarePageInterface
     * @throws \Exception
     */
    private function getPage(string $code): RichSnippetAwarePageInterface
    {
        if (!array_key_exists($code, $this->pages)) {
            throw new \Exception(sprintf('No page found for code "%s"', $code));
        }

        return $this->pages[$code];
    }
}

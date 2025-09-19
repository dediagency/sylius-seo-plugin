<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Behat\Context\Ui\Shop;

use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop\PageCollection;
use Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop\SeoPage;
use Webmozart\Assert\Assert;

class SeoContext extends MinkContext
{
    public const RICHSNIPPET_BREADCRUMB = 'BreadcrumbList';

    public const RICHSNIPPET_PRODUCT = 'Product';

    public const RICHSNIPPET_COLLECTION_PAGE = 'CollectionPage';

    private PageCollection $pageCollection;

    private CurrentPageResolverInterface $currentPageResolver;

    public function __construct(
        PageCollection $pageCollection,
        CurrentPageResolverInterface $currentPageResolver,
    ) {
        $this->pageCollection = $pageCollection;

        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @Then it should access the following breadcrumb:
     */
    public function itShouldAccessTheBreadcrumb(TableNode $table): void
    {
        $richSnippets = $this->getCurrentPage()->getRichSnippetData();

        Assert::keyExists(
            $richSnippets,
            self::RICHSNIPPET_BREADCRUMB,
            'This page doesn\'t have the Breadcrumb Rich Snippet',
        );

        $expectedItemListElement = [];
        foreach ($table->getHash() as $k => $item) {
            $itemData = [
                '@type' => 'ListItem',
                'position' => $k + 1,
                'name' => $item['name'],
            ];

            if (array_key_exists('url', $item) && !empty($item['url'])) {
                $itemData['item'] = $this->locatePath($item['url']);
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
            'Expected breadcrumb Rich Snippet and resolved breadcrumb Rich Snippet do not match',
        );
    }

    /**
     * @Then /^it should access the product named "([^"]*)" with the description "([^"]*)", the image "([^"]*)", the currency "([^"]*)" and the following offers:$/
     */
    public function itShouldAccessTheProduct(string $name, string $description, string $image, string $currency, TableNode $table): void
    {
        $richSnippets = $this->getCurrentPage()->getRichSnippetData();

        Assert::keyExists(
            $richSnippets,
            self::RICHSNIPPET_PRODUCT,
            'This page doesn\'t have the Product Rich Snippet',
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
                        'url' => $this->getCurrentPage()->getCurrentUrl(),
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
            'Expected product Rich Snippet and resolved product Rich Snippet do not match',
        );
    }

    /**
     * @Then it should access the taxon collection rich snippet for :taxonName with the following subcategories:
     */
    public function itShouldAccessTheTaxonCollectionRichSnippet(string $taxonName, TableNode $table): void
    {
        $richSnippets = $this->getCurrentPage()->getRichSnippetData();

        Assert::keyExists(
            $richSnippets,
            self::RICHSNIPPET_COLLECTION_PAGE,
            'This page doesn\'t have the Taxon CollectionPage Rich Snippet',
        );

        $collectionSnippet = $richSnippets[self::RICHSNIPPET_COLLECTION_PAGE][0];

        Assert::same(
            $collectionSnippet['name'],
            $taxonName,
            sprintf('CollectionPage Rich Snippet name does not match, expected "%s".', $taxonName),
        );

        Assert::keyExists(
            $collectionSnippet,
            'mainEntity',
            'CollectionPage Rich Snippet should expose a mainEntity node for subcategories.',
        );

        Assert::same(
            $collectionSnippet['mainEntity']['itemListElement'],
            $this->buildExpectedSubcategories($table),
            'Unexpected subcategories structure in CollectionPage Rich Snippet.',
        );
    }

    /**
     * @Then the taxon collection rich snippet should reference parent :parentName with url :url
     */
    public function theTaxonCollectionRichSnippetShouldReferenceParent(string $parentName, string $url): void
    {
        $richSnippets = $this->getCurrentPage()->getRichSnippetData();

        Assert::keyExists(
            $richSnippets,
            self::RICHSNIPPET_COLLECTION_PAGE,
            'This page doesn\'t have the Taxon CollectionPage Rich Snippet',
        );

        $collectionSnippet = $richSnippets[self::RICHSNIPPET_COLLECTION_PAGE][0];

        Assert::keyExists(
            $collectionSnippet,
            'isPartOf',
            'CollectionPage Rich Snippet should expose an isPartOf node for parent taxon.',
        );

        Assert::same(
            $collectionSnippet['isPartOf']['name'] ?? null,
            $parentName,
            'Unexpected parent name in CollectionPage Rich Snippet.',
        );

        if (array_key_exists('url', $collectionSnippet['isPartOf'])) {
            Assert::same(
                $collectionSnippet['isPartOf']['url'],
                $this->locatePath($url),
                'Unexpected parent url in CollectionPage Rich Snippet.',
            );
        }
    }

    private function buildExpectedSubcategories(TableNode $table): array
    {
        $expected = [];
        $position = 1;

        foreach ($table->getHash() as $row) {
            $itemData = [
                '@type' => 'ListItem',
                'position' => $position++,
                'item' => array_filter([
                    '@type' => 'CollectionPage',
                    'name' => $row['name'] ?? null,
                    'url' => ($row['url'] ?? null) ? $this->locatePath($row['url']) : null,
                ]),
            ];

            $expected[] = $itemData;
        }

        return $expected;
    }

    /**
     * @Then /^it should have the following og data:$/
     */
    public function itShouldHaveTheOgTitleAndTheOgUrl(TableNode $table)
    {
        $data = $this->getCurrentPage()->getOgData();

        foreach ($table->getHash() as $ogDatum) {
            Assert::keyExists(
                $data,
                sprintf('og:%s', $ogDatum['name']),
                sprintf('This page doesn\'t have an og:%s meta tag', $ogDatum['name']),
            );

            if ('url' === $ogDatum['name']) {
                $ogDatum['data'] = $this->locatePath($ogDatum['data']);
            }

            if (preg_match('/\%([a-z]+)\%/', $ogDatum['data'], $matches)) {
                $type = $matches[1];
                Assert::$type($ogDatum['data']);
            } else {
                Assert::same(
                    $data[sprintf('og:%s', $ogDatum['name'])],
                    $ogDatum['data'],
                    sprintf('Invalid og:%s, expected "%s", got "%s"', $ogDatum['name'], $ogDatum['data'], $data[sprintf('og:%s', $ogDatum['name'])]),
                );
            }
        }
    }

    /**
     * @Then I should be able to read a canonical URL tag with value :link
     */
    public function iShouldBeAbleToReadACanonicalUrlTagWithValue($link)
    {
        $currentPage = $this->getCurrentPage();
        Assert::true($currentPage->hasLinkRelCanonical());
        Assert::eq($currentPage->getLinkRelCanonical(), $this->locatePath($link));
    }

    /**
     * @Then I should be able to read an alternate URL tag with value :url and hreflang attribute value :hreflang
     */
    public function iShouldBeAbleToReadAnAlternateUrlTagWithValueAndHreflangAttributeValue(string $url, string $hreflang)
    {
        $currentPage = $this->getCurrentPage();
        Assert::true($currentPage->hasLinkAlternateForLocale($hreflang));
        Assert::eq($currentPage->getLinkRelAlternateForLocale($hreflang), $this->locatePath($url));
    }

    /**
     * @When I visit the homepage
     */
    public function iVisitTheHomepage()
    {
        $this->pageCollection->getPage('home')->open();
    }

    /**
     * @Then I should be able to read a no index no follow meta tag
     */
    public function iShouldBeAbleToReadANoIndexNoFollowMetaTag()
    {
        $currentPage = $this->getCurrentPage();
        Assert::true($currentPage->hasNoIndexNoFollowTag());
    }

    /**
     * @Then I should not be able to read a no index no follow meta tag
     */
    public function iShouldNotBeAbleToReadANoIndexNoFollowMetaTag()
    {
        $currentPage = $this->getCurrentPage();
        Assert::false($currentPage->hasNoIndexNoFollowTag());
    }

    private function getCurrentPage(): SeoPage
    {
        /** @var SeoPage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm($this->pageCollection->getAll());
        Assert::isInstanceOf($currentPage, SeoPage::class);

        return $currentPage;
    }
}

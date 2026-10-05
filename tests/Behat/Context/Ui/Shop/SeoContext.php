<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Behat\Context\Ui\Shop;

use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop\PageCollection;
use Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop\SeoPage;
use Webmozart\Assert\Assert;

class SeoContext extends MinkContext
{
    public const RICHSNIPPET_BREADCRUMB = 'BreadcrumbList';

    public const RICHSNIPPET_PRODUCT = 'Product';

    private PageCollection $pageCollection;

    private UrlMatcherInterface $urlMatcher;

    public function __construct(
        PageCollection $pageCollection,
        UrlMatcherInterface $urlMatcher,
    ) {
        $this->pageCollection = $pageCollection;

        $this->urlMatcher = $urlMatcher;
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

            if (array_key_exists('url', $item) && '' !== $item['url']) {
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
            sprintf(
                'Expected breadcrumb Rich Snippet and resolved breadcrumb Rich Snippet do not match. Expected: %s, got: %s',
                json_encode($expected),
                json_encode($richSnippets[self::RICHSNIPPET_BREADCRUMB]),
            ),
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
            sprintf(
                'Expected product Rich Snippet and resolved product Rich Snippet do not match. Expected: %s, got: %s',
                json_encode($expected),
                json_encode($richSnippets[self::RICHSNIPPET_PRODUCT]),
            ),
        );
    }

    /**
     * @Then /^it should have the following og data:$/
     */
    public function itShouldHaveTheOgTitleAndTheOgUrl(TableNode $table): void
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

            if (1 === preg_match('/\%([a-z]+)\%/', $ogDatum['data'], $matches)) {
                $assertion = [Assert::class, $matches[1]];
                Assert::true(is_callable($assertion), sprintf('Unknown assertion "%s"', $matches[1]));
                $assertion($data[sprintf('og:%s', $ogDatum['name'])]);
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
    public function iShouldBeAbleToReadACanonicalUrlTagWithValue(string $link): void
    {
        $currentPage = $this->getCurrentPage();
        Assert::true($currentPage->hasLinkRelCanonical());
        Assert::eq($currentPage->getLinkRelCanonical(), $this->locatePath($link));
    }

    /**
     * @Then I should be able to read an alternate URL tag with value :url and hreflang attribute value :hreflang
     */
    public function iShouldBeAbleToReadAnAlternateUrlTagWithValueAndHreflangAttributeValue(string $url, string $hreflang): void
    {
        $currentPage = $this->getCurrentPage();
        Assert::true($currentPage->hasLinkAlternateForLocale($hreflang));
        Assert::eq($currentPage->getLinkRelAlternateForLocale($hreflang), $this->locatePath($url));
    }

    /**
     * @When I visit the homepage
     */
    public function iVisitTheHomepage(): void
    {
        $this->pageCollection->getPage('home')->open();
    }

    /**
     * @Then I should be able to read a no index no follow meta tag
     */
    public function iShouldBeAbleToReadANoIndexNoFollowMetaTag(): void
    {
        $currentPage = $this->getCurrentPage();
        Assert::true($currentPage->hasNoIndexNoFollowTag());
    }

    /**
     * @Then I should not be able to read a no index no follow meta tag
     */
    public function iShouldNotBeAbleToReadANoIndexNoFollowMetaTag(): void
    {
        $currentPage = $this->getCurrentPage();
        Assert::false($currentPage->hasNoIndexNoFollowTag());
    }

    private function getCurrentPage(): SeoPage
    {
        $path = parse_url($this->getSession()->getCurrentUrl(), \PHP_URL_PATH);
        Assert::string($path);
        $routeName = $this->urlMatcher->match($path)['_route'] ?? null;

        foreach ($this->pageCollection->getAll() as $page) {
            if ($page instanceof SeoPage && $page->getRouteName() === $routeName) {
                return $page;
            }
        }

        throw new \LogicException(sprintf('Route "%s" could not be matched to any SEO page.', (string) $routeName));
    }
}

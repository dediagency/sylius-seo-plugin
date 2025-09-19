@seo @taxon
Feature: Check Taxon page Rich Snippets definition
  In order for the store to be as referenced as possible
  As a Googlebot
  I should have access to the defined Rich Snippets

  Background:
    Given the store operates on a single channel in the "United States" named "Fashion Web Store"
    And the store has "Category" taxonomy
    And I am logged in as an administrator
    And the "Category" taxon has children taxon "Caps" and "T-shirts"
    And the "Caps" taxon has children taxon "Simple" and "With pompons"

  @rich_snippets
  Scenario: Accessing the Breadcrumb Rich Snippets
    When I browse products from taxon "With pompons"
    Then it should access the following breadcrumb:
      | name         | url                                              |
      | Home         | /en_US/                     |
      | Category     | /en_US/taxons/category      |
      | Caps         | /en_US/taxons/caps |
      | With pompons |                                                  |

  @rich_snippets
  Scenario: Accessing the CollectionPage Rich Snippet for a parent taxon
    When I browse products from taxon "Caps"
    Then it should access the taxon collection rich snippet for "Caps" with the following subcategories:
      | name         | url                         |
      | Simple       | /en_US/taxons/simple        |
      | With pompons | /en_US/taxons/with-pompons |

  @rich_snippets
  Scenario: Accessing the CollectionPage Rich Snippet parent reference
    When I browse products from taxon "With pompons"
    Then the taxon collection rich snippet should reference parent "Caps" with url "/en_US/taxons/caps"

  @og_data
  Scenario: Accessing og data
    When I browse products from taxon "With pompons"
    Then it should have the following og data:
      | name  | data                                                 |
      | title | Fashion Web Store                                    |
      | url   | /en_US/taxons/with-pompons |

  @seo_links
  Scenario: Accessing the canonical URL in a taxon page
    When I browse products from taxon "Caps"
    Then I should be able to read a canonical URL tag with value "/en_US/taxons/caps"

  @seo_links
  Scenario: Accessing the canonical URL in filtered taxon page
    When I browse products from taxon "Caps"
    And I search for products with name "shirt"
    Then I should be able to read a canonical URL tag with value "/en_US/taxons/caps"

  @no_index_no_follow
  Scenario: Accessing a no index no follow meta tag in a taxon page
    When I browse products from taxon "Caps"
    Then I should not be able to read a no index no follow meta tag

  @no_index_no_follow
  Scenario: Accessing a no index no follow meta tag in a sorted taxon page
    Given the store has a product "Knitted wool-blend green cap"
    And the product "Knitted wool-blend green cap" has a main taxon "Caps"
    And this product is in "Caps" taxon at 1st position
    When I browse products from taxon "Caps"
    And I sort products alphabetically from a to z
    Then I should be able to read a no index no follow meta tag

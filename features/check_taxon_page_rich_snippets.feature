@seo @taxon
Feature: Check Taxon page Rich Snippets definition
  In order for the store to be as referenced as possible
  As a Googlebot
  I should have access to the defined Rich Snippets

  Background:
    Given the store operates on a single channel in the "United States" named "Fashion Web Store"
    And the store has "Category" taxonomy
    And the "Category" taxon has children taxon "Caps" and "T-shirts"
    And the "Caps" taxon has children taxon "Simple" and "With pompons"

  @rich_snippets @rich_snippets_breadcrumb
  Scenario: Accessing the Breadcrumb Rich Snippets
    When I browse products from taxon "With pompons"
    Then it should access the following breadcrumb:
      | name         | url                                              |
      | Home         | http://localhost:8080/en_US/                     |
      | Category     | http://localhost:8080/en_US/taxons/category      |
      | Caps         | http://localhost:8080/en_US/taxons/caps |
      | With pompons |                                                  |

  @og_data
  Scenario: Accessing og data
    When I browse products from taxon "With pompons"
    Then it should have the following og data:
      | name  | data                                                 |
      | title | Fashion Web Store                                    |
      | url   | http://localhost:8080/en_US/taxons/with-pompons |

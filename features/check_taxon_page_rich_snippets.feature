@seo @taxon
Feature: Check Taxon page Rich Snippets definition
  In order for the store to be as referenced as possible
  As a Googlebot
  I should have access to the defined Rich Snippets

  @rich_snippets @rich_snippets_breadcrumb
  Scenario: Accessing the Breadcrumb Rich Snippets
    When a Googlebot visits the taxon page with the following parameters:
      | slug              |
      | caps/with-pompons |
    Then it should access the following breadcrumb:
      | name         | url                                              |
      | Home         | http://localhost:8080/en_US/                     |
      | Category     | http://localhost:8080/en_US/taxons/category      |
      | Caps         | http://localhost:8080/en_US/taxons/category/caps |
      | With pompons |                                                  |

  @og_data
  Scenario: Accessing og data
    When a Googlebot visits the taxon page with the following parameters:
      | slug              |
      | caps/with-pompons |
    Then it should have the following og data:
      | name  | data                                                 |
      | title | Fashion Web Store                                    |
      | url   | http://localhost:8080/en_US/taxons/caps/with-pompons |

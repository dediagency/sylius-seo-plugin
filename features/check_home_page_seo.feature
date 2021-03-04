@seo @home
Feature: Check Homepage Rich Snippets definition
  In order for the store to be as referenced as possible
  As a Googlebot
  I should have access to the defined Rich Snippets and OG Data and SEO links

  Background:
    Given the store operates on a single channel in the "United States" named "Fashion Web Store"
    And that channel allows to shop using "English (United States)", "French (France)" and "Polish (Poland)" locales

  @rich_snippets
  Scenario: Accessing the Breadcrumb Rich Snippets
    When I visit the homepage
    Then it should access the following breadcrumb:
      | name |
      | Home |

  @og_data
  Scenario: Accessing og data
    When I visit the homepage
    Then it should have the following og data:
      | name  | data                         |
      | title | Fashion Web Store            |
      | url   | /en_US/ |

  @seo_links
  Scenario: Accessing the canonical URL
    When I visit the homepage
    Then I should be able to read a canonical URL tag with value "/en_US/"

  @seo_links
  Scenario: Accessing the alernate URLs
    When I visit the homepage
    Then I should be able to read an alternate URL tag with value "/fr_FR/" and hreflang attribute value "fr_fr"
    And I should be able to read an alternate URL tag with value "/pl_PL/" and hreflang attribute value "pl_pl"

  @seo_links
  Scenario: Accessing the alernate URLs after switching locale
    When I switch to the "Polish (Poland)" locale
    Then I should be able to read an alternate URL tag with value "/fr_FR/" and hreflang attribute value "fr_fr"
    And I should be able to read an alternate URL tag with value "/en_US/" and hreflang attribute value "en_us"
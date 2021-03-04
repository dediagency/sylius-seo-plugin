@seo @contact
Feature: Check Contact page Rich Snippets definition
  In order for the store to be as referenced as possible
  As a Googlebot
  I should have access to the defined Rich Snippets

  Background:
    Given the store operates on a single channel in the "United States" named "Fashion Web Store"

  @rich_snippets
  Scenario: Accessing the Breadcrumb Rich Snippets
    When I want to request contact
    Then it should access the following breadcrumb:
      | name       | url                          |
      | Home       | /en_US/ |
      | Contact us |                              |

  @og_data
  Scenario: Accessing og data
    When I want to request contact
    Then it should have the following og data:
    | name  | data                                 |
    | title | Fashion Web Store                    |
    | url   | /en_US/contact/ |

  @seo_links
  Scenario: Accessing the canonical URL in the contact page
    When I want to request contact
    Then I should be able to read a canonical URL tag with value "/en_US/contact/"
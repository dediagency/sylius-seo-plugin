@seo @home
Feature: Check Homepage Rich Snippets definition
  In order for the store to be as referenced as possible
  As a Googlebot
  I should have access to the defined Rich Snippets

  Background:
    Given the store operates on a single channel in the "United States" named "Fashion Web Store"

  @rich_snippets @rich_snippets_breadcrumb
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
      | url   | http://localhost:8080/en_US/ |

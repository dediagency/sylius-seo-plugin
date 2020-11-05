@seo @home
Feature: Check Homepage Rich Snippets definition
  In order for the store to be as referenced as possible
  As a Googlebot
  I should have access to the defined Rich Snippets

  @rich_snippets @rich_snippets_breadcrumb
  Scenario: Accessing the Breadcrumb Rich Snippets
    When a Googlebot visits the home page
    Then it should access the following breadcrumb:
      | name |
      | Home |

  @og_data
  Scenario: Accessing og data
    When a Googlebot visits the home page
    Then it should have the following og data:
      | name  | data                         |
      | title | Fashion Web Store            |
      | url   | http://localhost:8080/en_US/ |

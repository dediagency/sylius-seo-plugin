@seo @seo_links
Feature: Define a canonical URL for each page
  In order for the store to be as referenced as possible
  As a Googlebot
  I should have access to a canonical URL

    Background:
      Given the store operates on a single channel in the "United States" named "Fashion Web Store"
        And the store has a product "T-Shirt Dedi"
        And the store has "Clothes" taxonomy
        And the product "T-Shirt Dedi" has a main taxon "Clothes"

  @home
  Scenario: Accessing the canonical URL in the homepage
    When I visit the homepage
    Then I should be able to read a canonical URL tag with value "http://localhost:8080/en_US/"

  @product
  Scenario: Accessing the canonical URL in a product page
    When I view product "T-Shirt Dedi"
    Then I should be able to read a canonical URL tag with value "http://localhost:8080/en_US/products/t-shirt-dedi"

  @taxon
  Scenario: Accessing the canonical URL in a taxon page
    When I browse products from taxon "Clothes"
    Then I should be able to read a canonical URL tag with value "http://localhost:8080/en_US/taxons/clothes"

  @taxon
  Scenario: Accessing the canonical URL in filtered taxon page
    When I search for products with name "shirt"
    Then I should be able to read a canonical URL tag with value "http://localhost:8080/en_US/taxons/clothes"

  @contact
  Scenario: Accessing the canonical URL in the contact page
    When I want to request contact
    Then I should be able to read a canonical URL tag with value "http://localhost:8080/en_US/contact/"

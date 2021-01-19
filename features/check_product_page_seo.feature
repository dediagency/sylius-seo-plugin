@seo @product
Feature: Check Product page Rich Snippets definition
  In order for the store to be as referenced as possible
  As a Googlebot
  I should have access to the defined Rich Snippets

  Background:
    Given the store operates on a single channel in the "United States" named "Fashion Web Store"
    And the store has "Category" taxonomy
    And the "Category" taxon has children taxon "Jeans" and "Dresses"
    And the "Jeans" taxon has children taxon "Men" and "Women"
    And the store has a product "727F patched cropped jeans"
    And the product "727F patched cropped jeans" has a main taxon "Women"
    And the short description of product "727F patched cropped jeans" is "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."
#    And the "727F patched cropped jeans" product has an image "http://localhost:8080/media/cache/resolve/sylius_shop_product_large_thumbnail/727F_patched_cropped_jeans.jpeg" with "main" type

  @rich_snippets
  Scenario: Accessing the Breadcrumb Rich Snippets
    When I view product "727F patched cropped jeans"
    Then it should access the following breadcrumb:
      | name                       | url                                               |
      | Home                       | http://localhost:8080/en_US/                      |
      | Category                   | http://localhost:8080/en_US/taxons/category       |
      | Jeans                      | http://localhost:8080/en_US/taxons/jeans |
      | Women                      | http://localhost:8080/en_US/taxons/women    |
      | 727F patched cropped jeans |                                                   |

#  @rich_snippets @rich_snippets_product
#  Scenario: Accessing the Breadcrumb Rich Snippets
#    When I view product "727F patched cropped jeans"
#    Then it should access the product named "727F patched cropped jeans" with the description "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.", the image "http://localhost:8080/media/cache/resolve/sylius_shop_product_large_thumbnail/727F_patched_cropped_jeans.jpeg", the currency "USD" and the following offers:
#      | price | isInStock |
#      | 10.0 |          1 |
#      | 20.0 |          1 |
#      | 30.0 |          1 |
#      | 40.0 |          1 |
#      | 50.0 |          1 |

  @og_data
  Scenario: Accessing og data
    When I view product "727F patched cropped jeans"
    Then it should have the following og data:
      | name        | data                                                                                                                        |
      | title       | Women \| 727F patched cropped jeans                                                                                         |
      | url         | http://localhost:8080/en_US/products/727f-patched-cropped-jeans                                                             |
      | description | Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. |
#      | image       | http://localhost:8080/media/cache/resolve/sylius_shop_product_thumbnail/images/727F_patched_cropped_jeans.jpeg              |

  @seo_links
  Scenario: Accessing the canonical URL in a product page
    When I view product "727F patched cropped jeans"
    Then I should be able to read a canonical URL tag with value "http://localhost:8080/en_US/products/727f-patched-cropped-jeans"
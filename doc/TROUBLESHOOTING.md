# Troubleshooting

## The page title is not the SEO title

The `<title>` is rendered by `dedi_sylius_seo_get_title()` in your `base.html.twig` override. Check that:

- `templates/bundles/SyliusShopBundle/shared/layout/base.html.twig` is overridden (not extended), as described in [installation](INSTALL.md#override-default-layout-template),
- the block around the title is named `seo_title`, not `title`. Sylius templates redefine `{% block title %}`, which would replace the SEO title.

`dedi_sylius_seo_get_title('Sylius')` returns its argument when no title is found for the page.

## Metadata or OpenGraph tags are missing

- Check that the `#metatags`, `head` and `before_body` hooks are present in your `base.html.twig` override.
- Check that your `Product`, `Taxon` and `Channel` entities implement `ReferenceableInterface` and use the matching trait. The channel provides the fallback values for every page.
- A meta tag is only rendered when its value is not empty.

## An SEO content of type `uri` is not applied

The URL is compared with the full URL of the request, including the scheme, the host and the query string. Use the exact URL shown in the browser, for the right locale.

## Rich snippets are missing

- Check that `Product` implements `RichSnippetProductSubjectInterface` and `Taxon` implements `RichSnippetSubjectInterface`.
- Use the Symfony Profiler: the rich snippets panel lists the rich snippets generated for the page.

## The SEO tab is missing on product variants

The variant SEO tab is only displayed when your `ProductVariant` implements `RichSnippetProductVariantSubjectInterface`. See [installation](INSTALL.md#product-variant-identifiers-optional).

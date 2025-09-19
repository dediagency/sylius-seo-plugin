![docs/data/banner.png](doc/data/banner.png)

<h1 align="center">Sylius Plugin SEO by Dedi</h1>

<p>
Sylius SEO plugin by Dedi. Metadata, OpenGraph and RichSnippets (Breadcrumb and Product) for all Sylius resources
.</p>

<p>
Dedi - web agency specialized in eCommerce since 2004
UX, UI, Dev, SEO, SEA
</p>

## Overview

![docs/data/seo_plugin_example.png](doc/data/seo_plugin_example.png)

This Plugin provides an almost plug and play solution for your SEO needs. It integrates Metadata, OpenGraph and RichSnippets (Breadcrumb and Product) on all shop pages.

It provides integration for Google Analytics and Google Tag Manager through your channel configuration. 

## Organization Rich Snippet

The plugin now exposes an **Organization** schema based on the current channel. To enable it:

- Make your channel entity implement `RichSnippetChannelSubjectInterface`; the provided `RichSnippetChannelSubjectTrait` already handles the required getters.
- Ensure the channel data (name, description, email, phone, billing address/TVA) is filled in Sylius.
- Once done, the homepage, contact, product and taxon pages will automatically include the organization JSON-LD alongside existing rich snippets.

No extra configuration or manual fetch is required: the `OrganizationRichSnippetFactory` is registered by default and relies on the channel resolved from the `ChannelContextInterface`.

## Adding a custom Rich Snippet

Follow these steps to introduce a new JSON-LD block tailored to one of your business entities.

### 1. Make your entity a Rich Snippet subject

- Implement `RichSnippetSubjectInterface` (or create a dedicated interface extending it, as done for products and channels) on the entity you want to expose.
- Return a unique `getRichSnippetSubjectType()` string; factories rely on this value to know which subjects they can handle.
- Ensure your subject fetcher (see next step) returns an instance of that entity when the corresponding route is matched.

### 2. Provide a SubjectFetcher

- Implement `SubjectFetcherInterface` to locate or build the subject for a given request.
- Register the service with the `dedi_sylius_seo_plugin.rich_snippets.subject_fetcher` tag. The `RichSnippetContext` will iterate over all fetchers until one can serve the current request.
- Reuse existing patterns: check the route in `canFromRequest()` and load the entity in `fetchFromRequest()`.

### 3. Create a Rich Snippet factory

- Extend `AbstractRichSnippetFactory`, declare the handled subject types via `getHandledSubjectTypes()`, and implement `buildRichSnippet()` to return an object implementing `RichSnippetInterface` (create a new model if needed, inspired by `ProductRichSnippet` or `ChannelRichSnippet`).
- Inject any dependencies (router, price calculator, asset helpers, etc.) you need to build the final JSON-LD array.
- Register the factory service with the `dedi_sylius_seo_plugin.rich_snippets.factory` tag so it is picked up automatically.

### 4. Rendered output

- Twig automatically dumps every registered snippet with the `dedi_sylius_seo_get_rich_snippets()` function (`src/Resources/views/Shop/Header/_richSnippets.html.twig`). Once your subject fetcher and factory are in place, the JSON-LD block will appear without additional wiring.
- Optionally add functional tests or manual assertions to confirm the JSON-LD output matches your expectations.

## Documentation

- [Installation](doc/INSTALL.md)
- [Features](doc/FEATURES.md)
- [Contribute](doc/CONTRIBUTE.md)
- [Troubleshooting](doc/TROUBLESHOOTING.md)

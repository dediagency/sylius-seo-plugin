# API

The plugin exposes SEO content through Sylius API (API Platform). Resources are registered automatically when API Platform is enabled.

## Endpoints

| Method | Path                                         | Description                  |
|--------|----------------------------------------------|------------------------------|
| GET    | `/api/v2/admin/seo-contents/{id}`             | Get a SEO content            |
| PUT    | `/api/v2/admin/seo-contents/{id}`             | Update a SEO content         |
| GET    | `/api/v2/admin/seo-content-translations/{id}` | Get a SEO content translation |
| GET    | `/api/v2/shop/seo-contents/{id}`              | Get a SEO content            |
| GET    | `/api/v2/shop/seo-content-translations/{id}`  | Get a SEO content translation |

## Referenceable resources

Products, taxons and channels implementing `ReferenceableInterface` (with `ReferenceableTrait`) expose their SEO content
as `referenceableContent` on their own `show` endpoints. It is `null` until a SEO content has been saved.

It can be created or updated through the resource `PUT` endpoints:

```http
PUT /api/v2/admin/taxons/MY_TAXON
Content-Type: application/ld+json

{
    "referenceableContent": {
        "translations": {
            "en_US": {
                "locale": "en_US",
                "metadataTitle": "My title",
                "metadataDescription": "My description"
            }
        }
    }
}
```

To update an existing SEO content, pass its IRI and the IRI of the translations to update, as for any Sylius translation.
Without `@id`, a new SEO content is created and the previous one is deleted.

```json
{
    "referenceableContent": {
        "@id": "/api/v2/admin/seo-contents/1",
        "translations": {
            "en_US": {
                "@id": "/api/v2/admin/seo-content-translations/1",
                "metadataTitle": "My new title"
            }
        }
    }
}
```

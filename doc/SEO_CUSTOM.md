# SEO Usage for Custom Entity

Product, Taxon and Channel are supported out of the box. To add SEO content to another resource (e.g. a blog article), follow this cookbook.

In the examples below, the custom entity is `App\Entity\Article`, shown on the shop route `app_shop_article_show` with a `slug` parameter.

### 1 - Implement `ReferenceableInterface` in your entity

Use `ReferenceableTrait` and map the owning side of the relation to `SEOContent`. `inversedBy` is the name of the field you will add on `SEOContent` in step 2.

```php
namespace App\Entity;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Article implements ReferenceableInterface
{
    use ReferenceableTrait;

    #[ORM\OneToOne(inversedBy: 'article', targetEntity: SEOContentInterface::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'referenceableContent_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    protected ?SEOContentInterface $referenceableContent = null;

    // ...

    protected function createReferenceableContent(): SEOContentInterface
    {
        return new SEOContent();
    }
}
```

`ReferenceableTrait` adds all required methods. See [src/SEO/Adapter/ReferenceableTrait.php](../src/SEO/Adapter/ReferenceableTrait.php). To compute default values (e.g. the article title as metadata title), see [default values](DEFAULT_VALUES.md).

The locale of the SEO content is set automatically when the entity is loaded by Doctrine.

### 2 - Extend the `SEOContent` entity and add the inverse side

```php
namespace App\Entity;

use Dedi\SyliusSEOPlugin\Entity\SEOContent as BaseSEOContent;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'dedi_sylius_seo_content')]
class SEOContent extends BaseSEOContent
{
    #[ORM\OneToOne(mappedBy: 'referenceableContent', targetEntity: Article::class)]
    protected ?ReferenceableInterface $article = null;

    public function getArticle(): ?ReferenceableInterface
    {
        return $this->article;
    }

    public function setArticle(?ReferenceableInterface $article): static
    {
        if (null !== $this->article) {
            $this->article->setReferenceableContent(null);
        }
        if (null !== $article) {
            $article->setReferenceableContent($this);
        }
        $this->article = $article;

        return $this;
    }
}
```

Declare it as the SEO content model:

```yaml
# config/packages/dedi_sylius_seo_plugin.yaml
sylius_resource:
    resources:
        dedi_sylius_seo_plugin.seo_content:
            classes:
                model: App\Entity\SEOContent
```

The plugin's factory creates `Dedi\SyliusSEOPlugin\Entity\SEOContent` instances. Override it so new SEO contents (admin page, API) use your class:

```php
namespace App\Factory;

use App\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\Factory\SEOContentFactory as BaseSEOContentFactory;

class SEOContentFactory extends BaseSEOContentFactory
{
    public function createNew(): SEOContentInterface
    {
        return new SEOContent();
    }
}
```

```yaml
# config/services.yaml
services:
    dedi_sylius_seo_plugin.factory.seo_content:
        class: App\Factory\SEOContentFactory
        arguments:
            - '@sylius.provider.locale'
```

Also make `createReferenceableContent()` of your Product, Taxon and Channel entities return `App\Entity\SEOContent`.

### 3 - Add the `referenceableContent` field to your entity form type

Pass a `type` option to identify the kind of SEO content (it is stored on the SEO content and can be used to filter the admin grid).

```php
namespace App\Form\Type;

use Dedi\SyliusSEOPlugin\Form\Type\SEOContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('referenceableContent', SEOContentType::class, [
                'label' => 'dedi_sylius_seo_plugin.ui.seo',
                'constraints' => [new Valid()],
                'type' => 'article',
            ])
        ;
    }
}
```

### 4 - Add a metadata context

Create a class implementing `MetadataContextInterface` that returns the metadata of the article displayed on the current page. Throw `ContextNotAvailableInRequestException` when the current page is not an article page.

```php
namespace App\SEO\Context;

use App\Repository\ArticleRepositoryInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Context\MetadataContextInterface;
use Dedi\SyliusSEOPlugin\SEO\Exception\ContextNotAvailableInRequestException;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;
use Dedi\SyliusSEOPlugin\SEO\Transformer\ReferenceableToMetadataTransformerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

class ArticleMetadataContext implements MetadataContextInterface
{
    public function __construct(
        private readonly ArticleRepositoryInterface $repository,
        private readonly RequestStack $requestStack,
        private readonly ReferenceableToMetadataTransformerInterface $transformer,
    ) {
    }

    public function getMetadata(): Metadata
    {
        $request = $this->requestStack->getCurrentRequest();

        Assert::notNull($request);

        if ('app_shop_article_show' !== $request->attributes->get('_route')) {
            throw new ContextNotAvailableInRequestException();
        }

        $article = $this->repository->findOneBySlug((string) $request->attributes->get('slug'));

        Assert::isInstanceOf($article, ReferenceableInterface::class);

        return $this->transformer->transform($article);
    }
}
```

Declare the service with the `dedi_sylius_seo_plugin.context.metadata` tag. Use priority `0`, like the product and taxon contexts: SEO contents of type `uri` and noindex filters stay above it, and the channel metadata below it is used as fallback for empty fields.

```yaml
# config/services.yaml
services:
    app.seo.context.metadata.article:
        class: App\SEO\Context\ArticleMetadataContext
        arguments:
            - '@app.repository.article'
            - '@request_stack'
            - '@dedi_sylius_seo_plugin.transformer.referenceable_to_metadata'
        tags:
            - { name: dedi_sylius_seo_plugin.context.metadata, priority: 0 }
```

### 5 - Render the SEO form in your admin template

If your admin pages use Sylius Twig hooks, reuse the plugin templates to get the same layout and the Google / Twitter / Facebook preview as on taxons. Copy the hooks of [taxon_admin.yaml](../src/Resources/config/twig_hooks/taxon_admin.yaml), replacing `sylius_admin.taxon` with the hook prefix of your resource (e.g. `app_admin.article`), for both the `create` and `update` pages.

The section template `@DediSyliusSEOPlugin/admin/taxon/content/sections/seo_content.html.twig` only reads `form` and `resource` from the hook context, so it can be reused as is.

Otherwise, render the field directly:

```twig
{{ form_row(form.referenceableContent) }}
```

### 6 - Create migration

Create migration, review and execute them

```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

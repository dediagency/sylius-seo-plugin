
# SEO Usage for Custom Entity

To add SEO content administration for a Sylius resource, please follow this cookbook.

### 1 - Implement ReferenceableInterface into your Entity

```php

namespace App\Entity;

use App\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableTrait;
use Doctrine\ORM\Mapping as ORM;

class CustomEntity implements ReferenceableInterface
{
    use ReferenceableTrait;
    
    /**
     * @ORM\OneToOne(inversedBy="customEntity", targetEntity="Dedi\SyliusSEOPlugin\Entity\SEOContentInterface", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="referenceableContent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected ?SEOContentInterface $referenceableContent = null;

    protected function createReferenceableContent(): ReferenceableInterface
    {
        return new SEOContent();
    }
}
```

ReferenceableTrait add all required methods. All methods available here : [src/Domain/SEO/Adapter/ReferenceableTrait.php](src/Domain/SEO/Adapter/ReferenceableTrait.php)


### 2 - Extend `SEOContent` entity and add the relation

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dedi\SyliusSEOPlugin\Entity\SEOContent as BaseSEOContent;

class SEOContent extends BaseSEOContent
{
    /**
     * @ORM\OneToOne(mappedBy="referenceableContent", targetEntity="App\Entity\CustomEntity", cascade={"persist", "remove"})
     */
    protected ?ReferenceableInterface $customEntity = null;
    
    public function getCustomEntity(): ?ReferenceableInterface
    {
        return $this->customEntity;
    }

    public function setCustomEntity(?ReferenceableInterface $customEntity):void 
    {
        if (null !== $this->customEntity) {
            $this->customEntity->setReferenceableContent(null);
        }
        if (null !== $customEntity) {
            $customEntity->setReferenceableContent($this);
        }
        $this->customEntity = $customEntity;

        return $this;
    }
}
```


### 3 - Add `referenceableContent` field to your entity form type.

```php
namespace App\Form\Type;

use Dedi\SyliusSEOPlugin\Form\Type\AbstractReferenceableType;

class CustomEntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $build
            ->add('referenceableContent', SEOContentType::class, [
                'label' => 'dedi_sylius_seo_plugin.ui.seo',
                'constraints' => [new Valid()],
                'type' => 'customType',
            ])
        ;
    }
}
```

### 4 - Add metadata context

Create a class implementing `MetadataContextInterface` to retrieve CustomEntity metadata
```php
namespace App\SEO\Context;

use App\Repository\CustomEntityRepositoryInterface;
use Dedi\SyliusSEOPlugin\SEO\Context\MetadataContextInterface;
use Dedi\SyliusSEOPlugin\SEO\Transformer\ReferenceableToMetadataTransformerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

class CustomEntityMetadataContext implements MetadataContextInterface
{
    public function __construct(
        private CustomEntityRepositoryInterface $repository,
        private RequestStack $requestStack,
        private ReferenceableToMetadataTransformerInterface $transformer,
    ) {
    }
    
    public function getMetadata(): Metadata
    {
        $request = $this->requestStack->getCurrentRequest();

        Assert::notNull($request);

        // add custom logic to check if current page is in CustomEntity context
        if ($request->get('_route') !== 'app_custom_entity_index') {
            throw new ContextNotAvailableInRequestException();
        }
        
        $slug = $request->attributes->get('slug');

        Assert::string($slug);

        $customEntity = $this->repository->findOneBySlug($slug);

        Assert::isInstanceOf($customEntity, ReferenceableInterface::class);

        return $this->transformer->transform($customEntity);
    }
}
```

Declare service in `config/services.yaml` with `dedi_sylius_seo_plugin.context.metadata` tag

```yaml
services:
  ...

  app.context.metadata.custom_entity:
    class: App\SEO\Context\CustomEntityMetadataContext
    arguments:
      - @app.repository.custom_entity
      - @request_stack
      - @dedi_sylius_seo_plugin.transformer.referenceable_to_metadata
    tags:
      - {name: dedi_sylius_seo_plugin.context.metadata}
```


### 5 - Edit admin template to add SEO content form

```twig
...
<div class="ui hidden divider"></div>

<div class="ui segment">
    {{ form_row(form.referenceableContent) }}
</div>
```

### 6 - Create migration

Create migration, review and execute them

```bash
bin/console doctrine:migration:diff
bin/console doctrine:migration:migrate
```
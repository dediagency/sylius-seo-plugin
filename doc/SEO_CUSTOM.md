
# SEO Usage for Custom Entity

To add SEO content administration for a Sylius resource, please follow this cookbook.

### 1 - Implement ReferenceableInterface into your Entity

```php
use Dedi\SyliusSEOPlugin\Entity\SEOContent;use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableTrait;

class CustomEntity implements ReferenceableInterface
{
    use ReferenceableTrait;

    protected function createReferenceableContent(): ReferenceableInterface
    {
        return new SEOContent();
    }
}
```

ReferenceableTrait add all required methods. All methods available here : [src/Domain/SEO/Adapter/ReferenceableTrait.php](src/Domain/SEO/Adapter/ReferenceableTrait.php)

### 2 - Extend your form type with AbstractReferenceableType or AbstractReferenceableTypeExtension for form type extension

```php
use Dedi\SyliusSEOPlugin\Form\Type\AbstractReferenceableType;

class CustomEntityType extends AbstractReferenceableType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }
}
```

Or for form type extension

```php
use Dedi\SyliusSEOPlugin\Form\Extension\AbstractReferenceableTypeExtension;

class CustomEntityTypeExtension extends AbstractReferenceableTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        // ...
    }

    public static function getExtendedTypes(): iterable
    {
        return [CustomEntity::class];
    }
}
```

### 3 - Edit admin template to add SEO content form

```twig
...
<div class="ui hidden divider"></div>

<div class="ui segment">
    {{ form_row(form.referenceableContent) }}
</div>
```

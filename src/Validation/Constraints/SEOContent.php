<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class SEOContent extends Constraint
{
    public string $product_is_mandatory_message = 'dedi_sylius_seo_plugin.seo_content.product_is_mandatory';

    public string $taxon_is_mandatory_message = 'dedi_sylius_seo_plugin.seo_content.taxon_is_mandatory';
}

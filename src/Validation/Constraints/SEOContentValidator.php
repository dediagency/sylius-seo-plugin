<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Validation\Constraints;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\Enum\SEOContentTypeEnum;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SEOContentValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof SEOContent) {
            throw new UnexpectedTypeException($constraint, SEOContent::class);
        }

        if (!$value instanceof SEOContentInterface) {
            return;
        }

        $type = $value->getType();

        if ($type === SEOContentTypeEnum::PRODUCT->value && null === $value->getProduct()) {
            $this->context->buildViolation($constraint->product_is_mandatory_message)
                ->atPath('product')
                ->addViolation();
        }

        if ($type === SEOContentTypeEnum::TAXON->value && null === $value->getTaxon()) {
            $this->context->buildViolation($constraint->taxon_is_mandatory_message)
                ->atPath('taxon')
                ->addViolation();
        }
    }
}

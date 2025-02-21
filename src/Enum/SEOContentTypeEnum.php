<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Enum;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\Entity\SEOContentTranslationInterface;

enum SEOContentTypeEnum: string
{
    case PRODUCT = 'product';
    case TAXON = 'taxon';
    case CHANNEL = 'channel';
    case URI = 'uri';

    public static function fromSEOContent(SEOContentInterface $SEOContent): ?static
    {
        /** @var SEOContentTranslationInterface $translation */
        foreach ($SEOContent->getTranslations() as $translation) {
            if ($translation->getUri() !== null) {
                return self::URI;
            }
        }

        if ($SEOContent->getProduct() !== null) {
            return self::PRODUCT;
        }
        if ($SEOContent->getTaxon() !== null) {
            return self::TAXON;
        }
        if ($SEOContent->getChannel() !== null) {
            return self::CHANNEL;
        }

        return null;
    }
}

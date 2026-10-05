<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114142339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Move SEO indexability to per-locale robots and set SEO content type';
    }

    public function up(Schema $schema): void
    {
        // Idempotent: robots already present for a (content, locale) pair are kept.
        $this->addSql('
            INSERT INTO dedi_sylius_seo_content_robots (seo_content_id, locale_code, is_not_indexable)
            SELECT t.translatable_id, t.locale, t.seo_not_indexable
            FROM dedi_sylius_seo_content_translation t
            WHERE NOT EXISTS (
                SELECT 1 FROM dedi_sylius_seo_content_robots r
                WHERE r.seo_content_id = t.translatable_id AND r.locale_code = t.locale
            )
        ');

        foreach (['product' => 'sylius_product', 'taxon' => 'sylius_taxon', 'channel' => 'sylius_channel'] as $type => $table) {
            $this->addSql(sprintf('
                UPDATE dedi_sylius_seo_content SET type = \'%s\'
                WHERE type IS NULL AND id IN (
                    SELECT referenceableContent_id FROM %s WHERE referenceableContent_id IS NOT NULL
                )
            ', $type, $table));
        }

        // Open Graph type is no longer translatable: keep the value of a channel default locale,
        // or any non-empty translated value when the default locale has none.
        $this->addSql('
            UPDATE dedi_sylius_seo_content SET og_metadata_type = COALESCE(
                (
                    SELECT MIN(t.seo_og_metadata_type) FROM dedi_sylius_seo_content_translation t
                    WHERE t.translatable_id = dedi_sylius_seo_content.id
                    AND t.seo_og_metadata_type IS NOT NULL AND t.seo_og_metadata_type <> \'\'
                    AND t.locale IN (
                        SELECT l.code FROM sylius_locale l
                        INNER JOIN sylius_channel c ON c.default_locale_id = l.id
                    )
                ),
                (
                    SELECT MIN(t.seo_og_metadata_type) FROM dedi_sylius_seo_content_translation t
                    WHERE t.translatable_id = dedi_sylius_seo_content.id
                    AND t.seo_og_metadata_type IS NOT NULL AND t.seo_og_metadata_type <> \'\'
                )
            )
            WHERE og_metadata_type IS NULL
        ');
    }

    public function down(Schema $schema): void
    {
    }
}

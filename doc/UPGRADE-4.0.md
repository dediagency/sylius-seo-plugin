# Upgrade from < 4.0 to 4.0

## Update the plugin

```bash
composer update dedi/sylius-seo-plugin
```

## Copy migration file to your project

>Columns containing indexability data have been move from the `dedi_sylius_seo_content_translation` table to the `dedi_sylius_seo_content_robots` table.<br>
>This make it possible to define indexability data for each locale, even if the SEO content is not translated.<br><br>
>To update your database schema, you need to copy the migration file to your project.

Copy migration file from `vendor/dedi/sylius-seo-plugin/src/Migrations/` to your project's doctrine migrations directory.<br> :

```bash
cp -r vendor/dedi/sylius-seo-plugin/src/Migrations/* src/Migrations/
```

Change the namespace of the files to match your project namespace.

## Run doctrine migrations

```bash
bin/console doctrine:migrations:migrate
```
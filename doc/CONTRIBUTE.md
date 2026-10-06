# Contribute

## Documentation

For a comprehensive guide on Sylius Plugins development please go to Sylius documentation,
there you will find the <a href="https://docs.sylius.com/en/latest/plugin-development-guide/index.html">Plugin Development Guide</a>, that is full of examples.

## Quickstart Installation

The test application lives in `tests/Application` and runs with Docker.

```bash
$ make coffee   # build and start containers, install dependencies and assets
$ docker compose exec php bin/console doctrine:database:create
$ docker compose exec php bin/console doctrine:schema:update --force
$ docker compose exec php bin/console sylius:fixtures:load
```

Console commands run from `tests/Application`, which is the working directory of the `php` container.

## Usage

### Opening Sylius with your plugin

With the containers running, go to http://localhost/ (admin: http://localhost/admin, `sylius` / `sylius`).

### Running plugin tests

  - All checks (coding style, static analysis, specs)

    ```bash
    $ make test
    ```

  - PHPSpec

    ```bash
    $ make test-spec
    ```

  - PHPStan

    ```bash
    $ make phpstan
    ```

  - Behat

    ```bash
    $ ENV=test make test-behat-all
    $ ENV=test make test-behat TAGS="@seo"
    ```

Run `make help` to list all available commands.

### Contribution
    
Learn more about our contribution workflow on http://docs.sylius.org/en/latest/contributing/.

- [Learn how to create new RichSnippets](RICH_SNIPPETS.md)

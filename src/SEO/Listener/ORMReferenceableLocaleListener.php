<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Listener;

use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Sylius\Component\Core\Checker\CLIContextCheckerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;

class ORMReferenceableLocaleListener
{
    public function __construct(
        private readonly LocaleContextInterface $localeContext,
        private readonly TranslationLocaleProviderInterface $translationLocaleProvider,
        private readonly CLIContextCheckerInterface $commandBasedChecker,
    ) {
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof ReferenceableInterface) {
            return;
        }

        $fallbackLocale = $this->translationLocaleProvider->getDefaultLocaleCode();
        $entity->setReferenceableFallbackLocale($fallbackLocale);

        if ($this->commandBasedChecker->isExecutedFromCLI()) {
            $entity->setReferenceableLocale($fallbackLocale);

            return;
        }

        try {
            $currentLocale = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException) {
            $currentLocale = $fallbackLocale;
        }

        $entity->setReferenceableLocale($currentLocale);
    }
}

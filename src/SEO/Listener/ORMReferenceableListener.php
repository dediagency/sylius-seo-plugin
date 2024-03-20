<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Listener;

use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;
use Sylius\Component\Core\Checker\CLIContextCheckerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

class ORMReferenceableListener implements EventSubscriber
{
    public function __construct(
        private LocaleContextInterface $localeContext,
        private TranslationLocaleProviderInterface $translationLocaleProvider,
        private CLIContextCheckerInterface $commandBasedChecker,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
        ];
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

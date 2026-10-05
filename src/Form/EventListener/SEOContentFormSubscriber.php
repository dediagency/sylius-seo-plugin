<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\EventListener;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SEOContentFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SET_DATA => 'postSetData',
        ];
    }

    public function postSetData(FormEvent $event): void
    {
        /** @var SEOContentInterface $seo */
        $seo = $event->getData();
        $form = $event->getForm();

        $options = $event->getForm()->getConfig()->getOptions();

        $form->add('type', HiddenType::class, [
            'data' => $options['type'] ?? $seo->getType(),
        ]);
    }
}

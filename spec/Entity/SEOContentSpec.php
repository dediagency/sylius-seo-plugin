<?php

declare(strict_types=1);

namespace spec\Dedi\SyliusSEOPlugin\Entity;

use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentRobot;
use PhpSpec\ObjectBehavior;

class SEOContentSpec extends ObjectBehavior
{
    function let()
    {
        $this->setCurrentLocale('fr_FR');
        $this->setFallbackLocale('en_US');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SEOContent::class);
    }

    function it_is_indexable_by_default()
    {
        $this->isNotIndexable()->shouldReturn(false);
    }

    function it_uses_the_current_locale_robot()
    {
        $this->addRobot($this->createRobot('en_US', false));
        $this->addRobot($this->createRobot('fr_FR', true));

        $this->isNotIndexable()->shouldReturn(true);
    }

    function it_falls_back_to_the_fallback_locale_robot()
    {
        $this->addRobot($this->createRobot('en_US', true));

        $this->getRobot()->shouldReturn(null);
        $this->isNotIndexable()->shouldReturn(true);
    }

    function it_prefers_an_explicit_current_locale_robot_over_the_fallback()
    {
        $this->addRobot($this->createRobot('en_US', true));
        $this->addRobot($this->createRobot('fr_FR', false));

        $this->isNotIndexable()->shouldReturn(false);
    }

    function it_creates_a_robot_for_the_current_locale_when_setting_indexability()
    {
        $this->setNotIndexable(true);

        $this->getRobots()->count()->shouldReturn(1);
        $this->getRobot()->getLocale()->shouldReturn('fr_FR');
        $this->isNotIndexable()->shouldReturn(true);
    }

    function it_updates_the_existing_current_locale_robot_when_setting_indexability()
    {
        $this->addRobot($this->createRobot('fr_FR', true));

        $this->setNotIndexable(false);

        $this->getRobots()->count()->shouldReturn(1);
        $this->isNotIndexable()->shouldReturn(false);
    }

    private function createRobot(string $locale, bool $notIndexable): SEOContentRobot
    {
        $robot = new SEOContentRobot();
        $robot->setLocale($locale);
        $robot->setNotIndexable($notIndexable);

        return $robot;
    }
}

<?php

declare(strict_types=1);

namespace spec\Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\Factory\SEOContentFactory;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

class SEOContentFactorySpec extends ObjectBehavior
{
    function let(LocaleProviderInterface $localeProvider)
    {
        $this->beConstructedWith($localeProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SEOContentFactory::class);
    }

    function it_creates_new_seo_content_instance()
    {
        $this->createNew()->shouldBeAnInstanceOf(SEOContentInterface::class);
    }

    function it_creates_typed_seo_content_instance(LocaleProviderInterface $localeProvider)
    {
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $seoContent = $this->createTyped('product');

        $seoContent->shouldBeAnInstanceOf(SEOContent::class);
        $seoContent->getType()->shouldReturn('product');
    }
}

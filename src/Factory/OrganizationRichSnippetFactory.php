<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetChannelSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\AbstractRichSnippetFactory;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippet\ChannelRichSnippet;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class OrganizationRichSnippetFactory extends AbstractRichSnippetFactory
{
    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly RouterInterface $router,
    ) {
    }

    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        Assert::isInstanceOf($channel, RichSnippetChannelSubjectInterface::class);

        $data = array_filter([
            'name' => $channel->getName(),
            'description' => $channel->getDescription(),
            'code' => $channel->getCode(),
            'url' => $this->router->generate('sylius_shop_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ], static fn ($value) => null !== $value && '' !== $value);

        $richSnippet = new ChannelRichSnippet($data);

        if (null !== $email = $channel->getContactEmail()) {
            $richSnippet->addData(['email' => $email]);
        }

        if (null !== $phoneNumber = $channel->getContactPhoneNumber()) {
            $richSnippet->addData(['telephone' => $phoneNumber]);
        }

        if (($shopBillingData = $channel->getShopBillingData()) instanceof ShopBillingDataInterface) {
            $this->addBillingData($richSnippet, $shopBillingData);
        }

        return $richSnippet;
    }

    protected function getHandledSubjectTypes(): array
    {
        return ['homepage', 'contact', 'product', 'taxon', 'channel'];
    }

    private function addBillingData(ChannelRichSnippet $richSnippet, ShopBillingDataInterface $billingData): void
    {
        if (null !== $company = $billingData->getCompany()) {
            $richSnippet->addData(['legalName' => $company]);
        }

        if (null !== $vatNumber = $billingData->getTaxId()) {
            $richSnippet->addData(['vatID' => $vatNumber]);
        }

        $address = array_filter([
            'streetAddress' => $billingData->getStreet(),
            'postalCode' => $billingData->getPostcode(),
            'addressLocality' => $billingData->getCity(),
            'addressCountry' => $billingData->getCountryCode(),
        ], static fn ($value) => null !== $value && '' !== $value);

        if ([] !== $address) {
            $richSnippet->addData([
                'address' => array_merge(['@type' => 'PostalAddress'], $address),
            ]);
        }
    }
}

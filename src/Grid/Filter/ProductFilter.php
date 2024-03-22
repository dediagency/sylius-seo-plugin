<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Webmozart\Assert\Assert;

class ProductFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        Assert::string($data);

        if (strlen($data) > 0) {
            $dataSource->restrict(
                $dataSource->getExpressionBuilder()->in(
                    'product.code',
                    [$data],
                ),
            );
        }
    }
}

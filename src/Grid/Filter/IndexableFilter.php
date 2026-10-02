<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Webmozart\Assert\Assert;

class IndexableFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        Assert::isArray($data);

        if (!array_key_exists('indexable', $data) || strlen($data['indexable']) === 0) {
            return;
        }

        $dataSource->restrict(
            $dataSource->getExpressionBuilder()->equals(
                'robot.notIndexable',
                !$data['indexable'],
            ),
        );
    }
}

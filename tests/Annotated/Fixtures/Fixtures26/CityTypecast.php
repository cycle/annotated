<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures26;

use Cycle\ORM\Parser\CastableInterface;
use Cycle\ORM\Parser\UncastableInterface;

final class CityTypecast implements CastableInterface, UncastableInterface
{
    private array $rules = [];

    public function cast(array $data): array
    {
        foreach (array_keys($this->rules) as $column) {
            if (!isset($data[$column])) {
                continue;
            }

            if (!\is_string($data[$column]) || $data[$column] === '') {
                $data[$column] = null;

                continue;
            }

            $data[$column] = City::fromString($data[$column]);
        }

        return $data;
    }

    public function uncast(array $data): array
    {
        foreach (array_keys($this->rules) as $column) {
            if (!isset($data[$column])) {
                continue;
            }

            $value = $data[$column];

            if (!$value instanceof City) {
                continue;
            }

            $data[$column] = (string) $value;
        }

        return $data;
    }

    public function setRules(array $rules): array
    {
        /** @var non-empty-string $rule */
        foreach ($rules as $key => $rule) {
            if ($rule !== 'city') {
                continue;
            }

            unset($rules[$key]);

            $this->rules[$key] = $rule;
        }

        return $rules;
    }
}

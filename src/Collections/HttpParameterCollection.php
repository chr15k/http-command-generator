<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Collections;

use Chr15k\HttpCommand\Contracts\Collection;
use Chr15k\HttpCommand\Utils\Type;

/**
 * @internal
 */
final class HttpParameterCollection implements Collection
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $items = [];

    public function add(string $key, string $value): self
    {
        if (! isset($this->items[$key])) {
            $this->items[$key] = [];
        }

        $this->items[$key][] = $value;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function get(string $key): array
    {
        return $this->items[$key] ?? [];
    }

    public function first(string $key): ?string
    {
        return $this->items[$key][0] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    /**
     * @param  array<string, array<int, string>|string>  $params
     */
    public function merge(array $params): static
    {
        $instance = new self;
        $instance->items = $this->items;

        foreach ($params as $key => $values) {
            if (Type::isStringable($key)) {
                foreach (Type::normalizeToStringableArray($values) as $value) {
                    $instance->add((string) $key, (string) $value);
                }
            }
        }

        return $instance;
    }

    public function mergeFromQueryString(string $query): static
    {
        $result = [];

        foreach (explode('&', $query) as $pair) {
            [$key, $value] = array_pad(explode('=', $pair, 2), 2, '');
            $key = urldecode((string) $key);
            $value = urldecode((string) $value);

            if (array_key_exists($key, $result)) {
                if (! is_array($result[$key])) {
                    $result[$key] = [$result[$key]];
                }
                $result[$key][] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return (new self)->merge($result);
    }

    public function toQueryString(bool $encode = true): string
    {
        $queryParts = [];

        foreach ($this->items as $key => $values) {
            foreach ($values as $value) {
                if ($encode) {
                    $key = rawurlencode((string) $key);
                    $value = rawurlencode((string) $value);
                }
                $queryParts[] = "{$key}={$value}";
            }
        }

        return implode('&', $queryParts);
    }
}

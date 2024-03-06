<?php

namespace CancioLabs\Ds\Bag;

use ArrayIterator;
use BackedEnum;
use CancioLabs\Ds\Bag\Exception\ElementNotFoundException;
use Traversable;

class Bag implements BagInterface
{

    private array $bag;

    public function __construct(array $bag = [])
    {
        $this->set($bag);
    }

    public function all(): array
    {
        return $this->bag;
    }

    public function keys(): array
    {
        return array_keys($this->bag);
    }

    public function values(): array
    {
        return array_values($this->bag);
    }

    public function set(array $bag): self
    {
        foreach ($bag as $key => $value) {
            $this->add($key, $value);
        }

        return $this;
    }

    public function add(string $key, mixed $value): BagInterface
    {
        $this->bag[$key] = $value;

        return $this;
    }

    public function getAlpha(string $key, ?string $default = null): string
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
    }

    public function getAlphaNum(string $key, ?string $default = null): string
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
    }

    public function getArray(string $key, array $default = []): array
    {
        return $this->has($key) ? (array) $this->bag[$key] : $default;
    }

    public function getBool(string $key, bool $default = false): bool
    {
        return $this->has($key) ? (bool) $this->bag[$key] : $default;
    }

    public function getEnum(string $key, string $enumFQN, ?BackedEnum $default = null): BackedEnum
    {
        if (!$this->has($key)) {
            return $default;
        }

        $value = $this->get($key);

        return $enumFQN::from($value);
    }

    public function getDigits(string $key, string $default = ''): string
    {
        return preg_replace('/\D/', '', $this->getString($key, $default));
    }

    public function getFloat(string $key, float $default = 0.0): float
    {
        return $this->has($key) ? (float) $this->bag[$key] : $default;
    }

    public function getInt(string $key, int $default = 0): int
    {
        return $this->has($key) ? (int) $this->bag[$key] : $default;
    }

    public function getString(string $key, string $default = ''): string
    {
        return $this->has($key) ? (string) $this->bag[$key] : $default;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->bag[$key] : $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->bag);
    }

    public function isEmpty(): bool
    {
        return empty($this->bag);
    }

    public function clear(): self
    {
        $this->bag = [];

        return $this;
    }

    public function remove(string $key): BagInterface
    {
        if (!$this->has($key)) {
            throw new ElementNotFoundException($key);
        }

        unset($this->bag[$key]);

        return $this;
    }

    public function count(): int
    {
        return count($this->bag);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->bag);
    }

}
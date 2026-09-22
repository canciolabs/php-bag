<?php

namespace CancioLabs\Ds\Bag;

use ArrayIterator;
use BackedEnum;
use CancioLabs\Ds\Bag\Exception\ElementNotFoundException;
use CancioLabs\Ds\Bag\Exception\EnumNotFoundException;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use Traversable;

class Bag implements BagInterface
{

    private array $bag = [];

    public function __construct(array $bag = [])
    {
        $this->set($bag);
    }

    public function getAll(): array
    {
        return $this->bag;
    }

    public function getKeys(): array
    {
        return array_keys($this->bag);
    }

    public function getValues(): array
    {
        return array_values($this->bag);
    }

    public function merge(array|BagInterface ...$bags): self
    {
        foreach ($bags as $bag) {
            if ($bag instanceof BagInterface) {
                $this->bag = array_merge($this->bag, $bag->toArray());
            } elseif (is_array($bag)) {
                $this->bag = array_merge($this->bag, $bag);
            }
        }

        return $this;
    }

    public function set(array $bag): self
    {
        $this->bag = [];

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

    public function getAlpha(string $key, ?string $default = ''): ?string
    {
        return $this->isSet($key) ? preg_replace('/[^[:alpha:]]/', '', $this->get($key)) : $default;
    }

    public function getAlphaNum(string $key, ?string $default = ''): ?string
    {
        return $this->isSet($key) ? preg_replace('/[^[:alnum:]]/', '', $this->get($key)) : $default;
    }

    public function getArray(string $key, ?array $default = []): ?array
    {
        return $this->isSet($key) ? (array) $this->get($key) : $default;
    }

    public function getBag(string $key, BagInterface|array|null $default = []): ?BagInterface
    {
        if (!$this->isSet($key)) {
            if ($default === null) {
                return null;
            }

            return $default instanceof BagInterface ? $default : new self((array) $default);
        }

        $value = $this->get($key);

        return new self($value instanceof BagInterface ? $value->toArray() : (array) $value);
    }

    public function getBool(string $key, ?bool $default = false): ?bool
    {
        return $this->isSet($key) ? (bool) $this->get($key) : $default;
    }

    public function getDateTime(string $key, ?string $format = 'Y-m-d H:i:s', ?DateTimeInterface $default = null): ?DateTimeInterface
    {
        if (!$this->isSet($key)) {
            return $default;
        }

        $value = $this->get($key);

        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            $dateTime = DateTime::createFromFormat($format ?? 'Y-m-d H:i:s', $value);
            $errors = DateTime::getLastErrors();

            if ($dateTime !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $dateTime;
            }
        }

        throw new InvalidArgumentException("The value for key '$key' is not a valid DateTime string or object.");
    }

    public function getEnum(string $key, string $enumFQN, ?BackedEnum $default = null): ?BackedEnum
    {
        if (!enum_exists($enumFQN) || !is_subclass_of($enumFQN, BackedEnum::class)) {
            throw new EnumNotFoundException($enumFQN);
        }

        if (!$this->isSet($key)) {
            return $default;
        }

        $value = $this->get($key);

        /** @var BackedEnum $enumFQN */
        return $enumFQN::from($value);
    }

    public function getDigits(string $key, ?string $default = ''): ?string
    {
        return $this->isSet($key) ? preg_replace('/\D/', '', $this->getString($key)) : $default;
    }

    public function getFloat(string $key, ?float $default = 0.0): ?float
    {
        return $this->isSet($key) ? (float) $this->get($key) : $default;
    }

    public function getInt(string $key, ?int $default = 0): ?int
    {
        return $this->isSet($key) ? (int) $this->get($key) : $default;
    }

    public function getJson(string $key, mixed $default = null, bool $assoc = true): mixed
    {
        if (!$this->isSet($key)) {
            return $default;
        }

        $value = $this->get($key);

        if (is_string($value)) {
            return json_decode($value, $assoc, 512, JSON_THROW_ON_ERROR);
        }

        throw new InvalidArgumentException("The value for key '$key' is not a valid JSON string.");
    }

    public function getString(string $key, ?string $default = ''): ?string
    {
        return $this->isSet($key) ? (string) $this->get($key) : $default;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $resolved = $this->resolveKey($key);

        return $resolved['exists'] && $resolved['value'] !== null ? $resolved['value'] : $default;
    }

    public function isSet(string $key): bool
    {
        $resolved = $this->resolveKey($key);

        return $resolved['exists'] && $resolved['value'] !== null;
    }

    public function has(string $key): bool
    {
        return $this->resolveKey($key)['exists'];
    }

    public function isEmpty(): bool
    {
        return empty($this->bag);
    }

    public function isNotEmpty(): bool
    {
        return !empty($this->bag);
    }

    public function clear(): self
    {
        $this->bag = [];

        return $this;
    }

    public function remove(string $key): BagInterface
    {
        if (array_key_exists($key, $this->bag)) {
            unset($this->bag[$key]);

            return $this;
        }

        $bag = &$this->bag;
        $segments = explode('.', $key);
        $lastSegment = array_pop($segments);

        foreach ($segments as $segment) {
            if (!is_array($bag) || !array_key_exists($segment, $bag)) {
                throw new ElementNotFoundException($key);
            }

            $bag = &$bag[$segment];
        }

        if (!is_array($bag) || !array_key_exists($lastSegment, $bag)) {
            throw new ElementNotFoundException($key);
        }

        unset($bag[$lastSegment]);

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

    public function filter(callable $criteria): self
    {
        $filteredBag = [];

        foreach ($this->bag as $key => $value) {
            if ($criteria($key, $value)) {
                $filteredBag[$key] = $value;
            }
        }

        return new self($filteredBag);
    }

    public function map(callable $callback): self
    {
        $mappedBag = [];

        foreach ($this->bag as $key => $value) {
            $mappedBag[$key] = $callback($key, $value);
        }

        return new self($mappedBag);
    }

    public function every(callable $criteria): bool
    {
        foreach ($this->bag as $key => $value) {
            if (!$criteria($key, $value)) {
                return false;
            }
        }

        return true;
    }

    public function some(callable $criteria): bool
    {
        foreach ($this->bag as $key => $value) {
            if ($criteria($key, $value)) {
                return true;
            }
        }

        return false;
    }

    public function toDotNotation(): BagInterface
    {
        $literalDotKeys = [];

        foreach ($this->bag as $key => $value) {
            if (is_string($key) && str_contains($key, '.') && (!is_array($value) || $value === [])) {
                $literalDotKeys[$key] = $value;
            }
        }

        $flatten = static function (array $items, string $prefix = '') use (&$flatten): array {
            $flattened = [];

            foreach ($items as $key => $value) {
                $dotKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;

                if (is_array($value) && $value !== []) {
                    $flattened = array_merge($flattened, $flatten($value, $dotKey));
                    continue;
                }

                $flattened[$dotKey] = $value;
            }

            return $flattened;
        };

        $this->bag = array_replace($flatten($this->bag), $literalDotKeys);

        return $this;
    }

    public function toArray(): array
    {
        return $this->bag;
    }

    public function toJson(): string
    {
        return json_encode($this->bag, JSON_THROW_ON_ERROR);
    }

    private function resolveKey(string $key): array
    {
        if (array_key_exists($key, $this->bag)) {
            return ['exists' => true, 'value' => $this->bag[$key]];
        }

        $value = $this->bag;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return ['exists' => false, 'value' => null];
            }

            $value = $value[$segment];
        }

        return ['exists' => true, 'value' => $value];
    }

}
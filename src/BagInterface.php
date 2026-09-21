<?php

namespace CancioLabs\Ds\Bag;

use BackedEnum;
use Countable;
use DateTimeInterface;
use IteratorAggregate;

interface BagInterface extends Countable, IteratorAggregate
{

    /**
     * Returns all elements of the bag.
     * Alias for toArray() method.
     */
    public function getAll(): array;

    /**
     * Returns all keys of the bag.
     * @return string[]
     */
    public function getKeys(): array;

    /**
     * Returns all values of the bag.
     */
    public function getValues(): array;

    /**
     * Merges the current bag with one or more other bags.
     */
    public function merge(self|array ...$bags): self;

    /**
     * Replaces the current bag with new key-value pairs.
     */
    public function set(array $bag): self;

    /**
     * Adds a new key-value pair to the bag.
     */
    public function add(string $key, mixed $value): self;

    /**
     * Returns the alpha value of a given key and the default value if the key doesn't exist.
     */
    public function getAlpha(string $key, ?string $default = null): ?string;

    /**
     * Returns the alphanumeric value of a given key and the default value if the key doesn't exist.
     */
    public function getAlphaNum(string $key, ?string $default = null): ?string;

    /**
     * Returns the array value of a given key and the default value if the key doesn't exist.
     */
    public function getArray(string $key, ?array $default = []): ?array;

    /**
     * Convert and return the array value of a given key into a BagInterface instance,
     * and the default value if the key doesn't exist.
     */
    public function getBag(string $key, ?array $default = []): ?self;

    /**
     * Returns the boolean value of a given key and the default value if the key doesn't exist.
     */
    public function getBool(string $key, ?bool $default = false): ?bool;

    public function getDateTime(string $key, ?string $format = 'Y-m-d H:i:s', ?DateTimeInterface $default = null): ?DateTimeInterface;

    /**
     * Returns an enum case of a given key and a backed enum full qualified name,
     * and the default value if the key doesn't exist.
     */
    public function getEnum(string $key, string $enumFQN, ?BackedEnum $default = null): ?BackedEnum;

    /**
     * Returns the digit value of a given key and the default value if the key doesn't exist.
     */
    public function getDigits(string $key, ?string $default = ''): ?string;

    /**
     * Returns the float value of a given key and the default value if the key doesn't exist.
     */
    public function getFloat(string $key, ?float $default = 0.0): ?float;

    /**
     * Returns the integer value of a given key and the default value if the key doesn't exist.
     */
    public function getInt(string $key, ?int $default = 0): ?int;

    /**
     * Returns the string value of a given key and the default value if the key doesn't exist.
     */
    public function getString(string $key, ?string $default = ''): ?string;

    /**
     * Returns the value of a given key and the default value if the key doesn't exist.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Checks if a given key exists and is not null.
     */
    public function isSet(string $key): bool;

    /**
     * Checks if a given key exists in the bag.
     */
    public function has(string $key): bool;

    /**
     * Checks if the bag is empty.
     */
    public function isEmpty(): bool;

    /**
     * Check if the bag is not empty.
     */
    public function isNotEmpty(): bool;

    /**
     * Removes all elements from the bag.
     */
    public function clear(): self;

    /**
     * Deletes an element from the bag.
     */
    public function remove(string $key): self;

    /**
     * Transform the bag into a dot notation bag.
     * @return self
     */
    public function toDotNotation(): self;

    /**
     * Transform the bag into an array.
     */
    public function toArray(): array;

}
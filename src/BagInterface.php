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
    public function getAlpha(string $key, ?string $default = ''): ?string;

    /**
     * Returns the alphanumeric value of a given key and the default value if the key doesn't exist.
     */
    public function getAlphaNum(string $key, ?string $default = ''): ?string;

    /**
     * Returns the array value of a given key and the default value if the key doesn't exist.
     */
    public function getArray(string $key, ?array $default = []): ?array;

    /**
     * Convert and return the array value of a given key into a BagInterface instance,
     * and the default value if the key doesn't exist.
     */
    public function getBag(string $key, BagInterface|array|null $default = []): ?self;

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

    public function getJson(string $key, mixed $default = null, bool $assoc = true): mixed;

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
     * Filters the bag based on a given criteria.
     * The criteria is a callable that receives the key and value of each element in the bag.
     * It returns a new bag containing only the elements that match the criteria.
     */
    public function filter(callable $criteria): self;

    /**
     * Applies a callback function to each element in the bag and returns a new bag of the results.
     * The callback function receives the key and value of each element in the bag.
     */
    public function map(callable $callback): self;

    /**
     * Returns true if all elements match the criteria.
     * The criteria is a callable that receives the key and value of each element in the bag.
     */
    public function every(callable $criteria): bool;

    /**
     * Returns true if it finds at least one element that matches the criteria.
     * The criteria is a callable that receives the key and value of each element in the bag.
     */
    public function some(callable $criteria): bool;

    /**
     * Transform the bag into a dot notation bag.
     * @return self
     */
    public function toDotNotation(): self;

    /**
     * Transform the bag into an array.
     */
    public function toArray(): array;

    /**
     * Transform the bag into a JSON string.
     * @throws \JsonException
     */
    public function toJson(): string;

}
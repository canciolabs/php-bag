<?php

namespace CancioLabs\Ds\Bag;

use BackedEnum;
use Countable;
use IteratorAggregate;

interface BagInterface extends Countable, IteratorAggregate
{

    /**
     * Returns all key-value pairs of the bag.
     */
    public function all(): array;

    /**
     * Returns all keys of the bag.
     * @return string[]
     */
    public function keys(): array;

    /**
    /**
     * Returns all values of the bag.
     */
    public function values(): array;

    /**
     * Replaces the current bag with new key-value pairs.
     */
    public function set(array $bag): self;

    /**
     * Adds a new key-value pair to the bag.
     */
    public function add(string $key, mixed $value): self;

    /**
     * Returns the alpha value of a given key.
     * Returns the default value if the key doesn't exist.
     */
    public function getAlpha(string $key, ?string $default = null): ?string;

    /**
     * Returns the alphanumeric value of a given key, and the default value if the key doesn't exist.
     */
    public function getAlphaNum(string $key, ?string $default = null): ?string;

    /**
     * Returns the array value of a given key, and the default value if the key doesn't exist.
     */
    public function getArray(string $key, ?array $default = []): ?array;

    /**
     * Returns the boolean value of a given key, and the default value if the key doesn't exist.
     */
    public function getBool(string $key, ?bool $default = false): ?bool;

    /**
     * Returns an enum case of a given key and a backed enum full qualified name,
     * and the default value if the key doesn't exist.
     */
    public function getEnum(string $key, string $enumFQN, ?BackedEnum $default = null): ?BackedEnum;

    /**
     * Returns the digit value of a given key, and the default value if the key doesn't exist.
     */
    public function getDigits(string $key, ?string $default = ''): ?string;

    /**
     * Returns the float value of a given key, and the default value if the key doesn't exist.
     */
    public function getFloat(string $key, ?float $default = 0.0): ?float;

    /**
     * Returns the integer value of a given key, and the default value if the key doesn't exist.
     */
    public function getInt(string $key, ?int $default = 0): ?int;

    /**
     * Returns the string value of a given key, and the default value if the key doesn't exist.
     */
    public function getString(string $key, ?string $default = ''): ?string;

    /**
     * Returns the value of a given key, and the default value if the key doesn't exist.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Checks if a given key exist in the bag.
     */
    public function has(string $key): bool;

    /**
     * Checks if the bag is empty.
     */
    public function isEmpty(): bool;

    /**
     * Removes all elements from the bag.
     */
    public function clear(): self;

    /**
     * Deletes an element from the bag.
     */
    public function remove(string $key): self;

    /**
     * Transform the bag into an array.
     */
    public function toArray(): array;

}
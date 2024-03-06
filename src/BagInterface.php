<?php

namespace CancioLabs\Ds\Bag;

use BackedEnum;
use Countable;
use IteratorAggregate;

interface BagInterface extends Countable, IteratorAggregate
{

    /**
     * Return all key-value pairs of the bag.
     */
    public function all(): array;

    /**
     * Return all keys of the bag.
     * @return string[]
     */
    public function keys(): array;

    /**
     * Return all values of the bag.
     * @return array
     */
    public function values(): array;

    /**
     * Replace the current bag with new key-value pairs.
     */
    public function set(array $bag): self;

    /**
     * Add a new key-value pair to the bag.
     */
    public function add(string $key, mixed $value): self;

    /**
     * Return the alpha value of a given key.
     */
    public function getAlpha(string $key, ?string $default = null): string;

    /**
     * Return the alphanumeric value of a given key.
     */
    public function getAlphaNum(string $key, ?string $default = null): string;

    /**
     * Return the array value of a given key.
     */
    public function getArray(string $key, array $default = []): array;

    /**
     * Return the boolean value of a given key.
     */
    public function getBool(string $key, bool $default = false): bool;

    /**
     * Return an enum case of a given key and a backed enum full qualified name.
     */
    public function getEnum(string $key, string $enumFQN, ?BackedEnum $default = null): BackedEnum;

    /**
     * Return the digit value of a given key.
     */
    public function getDigits(string $key, string $default = ''): string;

    /**
     * Return the float value of a given key.
     */
    public function getFloat(string $key, float $default = 0.0): float;

    /**
     * Return the integer value of a given key.
     */
    public function getInt(string $key, int $default = 0): int;

    /**
     * Return the string value of a given key.
     */
    public function getString(string $key, string $default = ''): string;

    /**
     * Return the value of a given key.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Check if a given key exist in the bag.
     */
    public function has(string $key): bool;

    /**
     * Check if the bag is empty.
     */
    public function isEmpty(): bool;

    /**
     * Remove all elements from the bag.
     */
    public function clear(): self;

    /**
     * Delete an element from the bag.
     */
    public function remove(string $key): self;

}
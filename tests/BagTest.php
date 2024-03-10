<?php

namespace Test\CancioLabs\Ds\Bag;

use ArrayIterator;
use CancioLabs\Ds\Bag\Bag;
use CancioLabs\Ds\Bag\Exception\ElementNotFoundException;
use PHPUnit\Framework\TestCase;

class BagTest extends TestCase
{

    public function testConstructor(): void
    {
        // When arg is empty
        $bag1 = new Bag();
        $this->assertTrue($bag1->isEmpty());

        // When arg not empty
        $bag2 = new Bag(['number' => 1, 'fruit' => 'banana']);
        $this->assertSame(['number' => 1, 'fruit' => 'banana'], $bag2->all());
    }

    public function testAll(): void
    {
        $bag = new Bag();

        // Initial state
        $this->assertSame([], $bag->all());

        // With one value
        $bag->add('number', 1);
        $this->assertSame([
            'number' => 1,
        ], $bag->all());

        // With two values
        $bag->add('fruit', 'banana');
        $this->assertSame([
            'number' => 1,
            'fruit' => 'banana',
        ], $bag->all());
    }

    public function testKeys(): void
    {
        $bag = new Bag();

        // Initial state
        $this->assertSame([], $bag->keys());

        // With one value
        $bag->add('number', 1);
        $this->assertSame(['number'], $bag->keys());

        // With two values
        $bag->add('fruit', 'banana');
        $this->assertSame(['number', 'fruit'], $bag->keys());
    }

    public function testValues(): void
    {
        $bag = new Bag();

        // Initial state
        $this->assertSame([], $bag->values());

        // With one value
        $bag->add('number', 1);
        $this->assertSame([1], $bag->values());

        // With two values
        $bag->add('fruit', 'banana');
        $this->assertSame([1, 'banana'], $bag->values());
    }

    public function testSet(): void
    {
        $bag = new Bag();

        $bag->set(['number' => 1, 'fruit' => 'banana']);
        $this->assertSame(['number' => 1, 'fruit' => 'banana'], $bag->all());

        $bag->set(['text' => 'Lorem ipsum...', 'is_created_by_human' => false]);
        $this->assertSame(['text' => 'Lorem ipsum...', 'is_created_by_human' => false], $bag->all());
    }

    public function testAddHasAndRemove(): void
    {
        $bag = new Bag();

        // Initial state
        $this->assertFalse($bag->has('number'));
        $this->assertFalse($bag->has('fruit'));

        // With one value
        $bag->add('number', 1);
        $this->assertTrue($bag->has('number'));
        $this->assertFalse($bag->has('fruit'));

        // With two values
        $bag->add('fruit', 'banana');
        $this->assertTrue($bag->has('number'));
        $this->assertTrue($bag->has('fruit'));

        // Remove one value
        $bag->remove('number');
        $this->assertFalse($bag->has('number'));
        $this->assertTrue($bag->has('fruit'));

        // Remove two values
        $bag->remove('fruit');
        $this->assertFalse($bag->has('number'));
        $this->assertFalse($bag->has('fruit'));
    }

    public function testRemoveWhenKeyDoesNotExist(): void
    {
        $bag = new Bag();

        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage('Unable to remove the element "number" as it was not found in the bag.');

        $bag->remove('number');
    }

    public function testGetBool(): void
    {
        $bag = new Bag();

        // Booleans
        $bag->add('true', true);
        $this->assertTrue($bag->getBool('true'));

        $bag->add('false', false);
        $this->assertFalse($bag->getBool('false'));

        // Default values
        $this->assertFalse($bag->getBool('default'));
        $this->assertTrue($bag->getBool('default', true));

        // Other data types
        $bag->add('one', 1);
        $this->assertTrue($bag->getBool('one'));

        $bag->add('zero', 0);
        $this->assertFalse($bag->getBool('zero'));

        $bag->add('emptyString', '');
        $this->assertFalse($bag->getBool('emptyString'));
    }

    public function testGetFloat(): void
    {
        $bag = new Bag();

        // Float
        $bag->add('float', 123.4);
        $this->assertSame(123.4, $bag->getFloat('float'));

        // Default values
        $this->assertSame(0.0, $bag->getFloat('default'));
        $this->assertSame(2.456, $bag->getFloat('default', 2.456));

        // Other data types
        $bag->add('false', false);
        $this->assertSame(0.0, $bag->getFloat('false'));

        $bag->add('int', 1);
        $this->assertSame(1.0, $bag->getFloat('int'));

        $bag->add('emptyString', '');
        $this->assertSame(0.0, $bag->getFloat('emptyString'));
    }

    public function testGetInt(): void
    {
        $bag = new Bag();

        // Integer
        $bag->add('int', 1);
        $this->assertSame(1, $bag->getInt('int'));

        // Default values
        $this->assertSame(0, $bag->getInt('default'));
        $this->assertSame(2, $bag->getInt('default', 2));

        // Other data types
        $bag->add('false', false);
        $this->assertSame(0, $bag->getInt('false'));

        $bag->add('float', 123.4);
        $this->assertSame(123, $bag->getInt('float'));

        $bag->add('emptyString', '');
        $this->assertSame(0, $bag->getInt('emptyString'));
    }

    public function testIsEmpty(): void
    {
        $bag = new Bag();

        // Initial state
        $this->assertTrue($bag->isEmpty());

        // With one value
        $bag->add('number', 1);
        $this->assertFalse($bag->isEmpty());

        // With two values
        $bag->add('fruit', 'banana');
        $this->assertFalse($bag->isEmpty());

        // Remove one value
        $bag->remove('number');
        $this->assertFalse($bag->isEmpty());

        // Remove two values
        $bag->remove('fruit');
        $this->assertTrue($bag->isEmpty());
    }

    public function testCount(): void
    {
        $bag = new Bag();

        // Initial state
        $this->assertCount(0, $bag);

        // With one value
        $bag->add('number', 1);
        $this->assertCount(1, $bag);

        // With two values
        $bag->add('fruit', 'banana');
        $this->assertCount(2, $bag);

        // Remove one value
        $bag->remove('number');
        $this->assertCount(1, $bag);

        // Remove two values
        $bag->remove('fruit');
        $this->assertCount(0, $bag);
    }

    public function testClear(): void
    {
        $bag = new Bag();

        $bag->set(['number' => 1, 'fruit' => 'banana']);
        $bag->clear();
        $this->assertSame([], $bag->all());

        $bag->set(['text' => 'Lorem ipsum...', 'is_created_by_human' => false]);
        $bag->clear();
        $this->assertSame([], $bag->all());
    }

    public function testGetIterator(): void
    {
        $bag = new Bag();

        $bag->add('number', 1);
        $bag->add('fruit', 'banana');

        $it = $bag->getIterator();

        $this->assertInstanceOf(ArrayIterator::class, $it);
    }

}
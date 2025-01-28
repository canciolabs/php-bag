<?php

namespace Test\CancioLabs\Ds\Bag;

use ArrayIterator;
use CancioLabs\Ds\Bag\Bag;
use CancioLabs\Ds\Bag\Exception\ElementNotFoundException;
use CancioLabs\Ds\Bag\Exception\EnumNotFoundException;
use PHPUnit\Framework\TestCase;

enum TestEnum: string
{
    case FOO = 'foo';
    case BAR = 'bar';
    case ZOO = 'zoo';
}

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

    public function testGetAlphaAndAlphaNumAndDigits(): void
    {
        $bag = new Bag();
        $bag->add('alpha', 'abcde');
        $bag->add('alpha_num', 'abcde456789');
        $bag->add('digits', '123456');

        // alpha
        $this->assertSame('abcde', $bag->getAlpha('alpha'));
        $this->assertSame('abcde', $bag->getAlpha('alpha_num'));
        $this->assertSame('', $bag->getAlpha('digits'));

        // alpha-num
        $this->assertSame('abcde', $bag->getAlphaNum('alpha'));
        $this->assertSame('abcde456789', $bag->getAlphaNum('alpha_num'));
        $this->assertSame('123456', $bag->getAlphaNum('digits'));

        // digits
        $this->assertSame('', $bag->getDigits('alpha'));
        $this->assertSame('456789', $bag->getDigits('alpha_num'));
        $this->assertSame('123456', $bag->getDigits('digits'));

        // alpha default values
        $this->assertSame('', $bag->getAlpha('default'));
        $this->assertSame('foo', $bag->getAlpha('default', 'foo'));
        $this->assertNull($bag->getAlpha('default', null));

        // alpha-num default values
        $this->assertSame('', $bag->getAlphaNum('default'));
        $this->assertSame('bar', $bag->getAlphaNum('default', 'bar'));
        $this->assertNull($bag->getAlphaNum('default', null));
    }

    public function testGetArray(): void
    {
        $bag = new Bag();

        // Array
        $bag->add('array', ['q', 'w', 'e', 'r', 't', 'y']);
        $this->assertSame(['q', 'w', 'e', 'r', 't', 'y'], $bag->getArray('array'));

        // Default values
        $this->assertSame([], $bag->getArray('default'));
        $this->assertSame([3, 4, 5], $bag->getArray('default', [3, 4, 5]));
        $this->assertNull($bag->getArray('default', null));
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
        $this->assertNull($bag->getBool('default', null));
    }

    public function testGetEnum(): void
    {
        $bag = new Bag();
        $bag->add('foo', TestEnum::FOO->value);
        $bag->add('bar', TestEnum::BAR->value);

        // Enum
        $this->assertSame(TestEnum::FOO, $bag->getEnum('foo', TestEnum::class));
        $this->assertSame(TestEnum::BAR, $bag->getEnum('bar', TestEnum::class));

        // Default values
        $this->assertNull($bag->getEnum('zoo', TestEnum::class));
        $this->assertSame(TestEnum::ZOO, $bag->getEnum('zoo', TestEnum::class, TestEnum::ZOO));
    }

    public function testGetEnumWhenEnumDoesNotExist(): void
    {
        $bag = new Bag();

        $this->expectException(EnumNotFoundException::class);

        $bag->getEnum('gas', 'MyDummyEnum');
    }

    public function testGetFloat(): void
    {
        $bag = new Bag();

        // Float
        $bag->add('float', 123.4);
        $this->assertSame(123.4, $bag->getFloat('float'));

        // Int
        $bag->add('int', 4);
        $this->assertSame(4.0, $bag->getFloat('int'));

        // Default values
        $this->assertSame(0.0, $bag->getFloat('default'));
        $this->assertSame(2.456, $bag->getFloat('default', 2.456));
        $this->assertNull($bag->getFloat('default', null));
    }

    public function testGetInt(): void
    {
        $bag = new Bag();

        // Integer
        $bag->add('int', 2);
        $this->assertSame(2, $bag->getInt('int'));

        // Float
        $bag->add('float', 123.4);
        $this->assertSame(123, $bag->getInt('float'));

        // Default values
        $this->assertSame(0, $bag->getInt('default'));
        $this->assertSame(3, $bag->getInt('default', 3));
        $this->assertNull($bag->getInt('default', null));
    }

    public function testGetString(): void
    {
        $bag = new Bag();

        // String
        $bag->add('string', 'abc');
        $this->assertSame('abc', $bag->getString('string'));

        // Default values
        $this->assertSame('', $bag->getString('default'));
        $this->assertSame('foo', $bag->getString('default', 'foo'));
        $this->assertNull($bag->getString('default', null));
    }

    public function testGet(): void
    {
        $bag = new Bag();

        $bag->add('alpha', 'zoo');
        $bag->add('alphanum', 'foo102030');
        $bag->add('array', [1, 2, 3]);
        $bag->add('true', true);
        $bag->add('false', false);
        //$bag->add('enums', '789.789');
        $bag->add('digits', '789.789');
        $bag->add('float', 456.789);
        $bag->add('int', 123);
        $bag->add('string', 'abc');

        $this->assertSame('zoo', $bag->get('alpha'));
        $this->assertSame('foo102030', $bag->get('alphanum'));
        $this->assertSame([1, 2, 3], $bag->get('array'));
        $this->assertSame(true, $bag->get('true'));
        $this->assertSame(false, $bag->get('false'));
        //$this->assertSame(false, $bag->get('enums'));
        $this->assertSame('789.789', $bag->get('digits'));
        $this->assertSame(456.789, $bag->get('float'));
        $this->assertSame(123, $bag->get('int'));
        $this->assertSame('abc', $bag->get('string'));
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

    public function testToArray(): void
    {
        $bag = new Bag();

        $this->assertSame([], $bag->toArray());

        $bag->add('string', 'abc');
        $bag->add('int', 7);
        $bag->add('float', 7.22);

        $this->assertSame([
            'string' => 'abc',
            'int' => 7,
            'float' => 7.22,
        ], $bag->toArray());

        $bag->clear();

        $this->assertSame([], $bag->toArray());
    }

}
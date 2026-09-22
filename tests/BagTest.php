<?php

namespace Test\CancioLabs\Ds\Bag;

use ArrayIterator;
use CancioLabs\Ds\Bag\Bag;
use CancioLabs\Ds\Bag\BagInterface;
use CancioLabs\Ds\Bag\Exception\ElementNotFoundException;
use CancioLabs\Ds\Bag\Exception\EnumNotFoundException;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\TestCase;
use stdClass;

enum TestEnum: string
{
    case Foo = 'foo';
    case Bar = 'bar';
    case Default = 'default';
}

enum UnbackedTestEnum
{
    case Value;
}

class BagTest extends TestCase
{
    public function testConstructingAndInspectingABag(): void
    {
        $emptyBag = new Bag();
        $bag = new Bag(['name' => 'Ada', 'active' => true]);

        $this->assertTrue($emptyBag->isEmpty());
        $this->assertSame(['name' => 'Ada', 'active' => true], $bag->getAll());
        $this->assertSame(['name', 'active'], $bag->getKeys());
        $this->assertSame(['Ada', true], $bag->getValues());
        $this->assertSame([10], (new Bag([10 => 'ten']))->getKeys());
        $this->assertSame($bag->getAll(), $bag->toArray());
        $this->assertCount(2, $bag);
        $this->assertInstanceOf(ArrayIterator::class, $bag->getIterator());
        $this->assertSame($bag->getAll(), iterator_to_array($bag));
    }

    public function testSettingAddingAndMergingValues(): void
    {
        $bag = new Bag(['obsolete' => true]);

        $this->assertSame($bag, $bag->set(['name' => 'Ada']));
        $this->assertSame($bag, $bag->add('role', 'Mathematician'));
        $this->assertSame(
            $bag,
            $bag->merge(['active' => true], new Bag(['name' => 'Ada Lovelace']))
        );

        $this->assertSame([
            'name' => 'Ada Lovelace',
            'role' => 'Mathematician',
            'active' => true,
        ], $bag->toArray());
    }

    public function testScalarAndSanitizingGetters(): void
    {
        $bag = new Bag([
            'alpha' => 'A-1_b!',
            'number' => '12.7',
            'truthy' => '1',
            'falsy' => false,
            'value' => 42,
            'array' => ['value'],
            'stringable' => new class implements \Stringable {
                public function __toString(): string
                {
                    return 'Stringable value';
                }
            },
        ]);

        $this->assertSame('Ab', $bag->getAlpha('alpha'));
        $this->assertSame('A1b', $bag->getAlphaNum('alpha'));
        $this->assertSame('', $bag->getAlpha('array'));
        $this->assertSame('', $bag->getAlphaNum('array'));
        $this->assertSame('1', $bag->getDigits('alpha'));
        $this->assertSame('', $bag->getDigits('array'));
        $this->assertSame(12, $bag->getInt('number'));
        $this->assertSame(12.7, $bag->getFloat('number'));
        $this->assertTrue($bag->getBool('truthy'));
        $this->assertFalse($bag->getBool('falsy'));
        $this->assertSame('42', $bag->getString('value'));
        $this->assertSame('Stringable value', $bag->getString('stringable'));
        $this->assertSame('', $bag->getString('array'));
        $this->assertSame('fallback', $bag->getString('array', 'fallback'));

        $this->assertSame('fallback', $bag->get('missing', 'fallback'));
        $this->assertSame('', $bag->getAlpha('missing'));
        $this->assertSame('fallback', $bag->getAlpha('missing', 'fallback'));
        $this->assertSame('', $bag->getAlphaNum('missing'));
        $this->assertSame('fallback', $bag->getAlphaNum('missing', 'fallback'));
        $this->assertSame('fallback', $bag->getDigits('missing', 'fallback'));
        $this->assertSame(99, $bag->getInt('missing', 99));
        $this->assertSame(9.9, $bag->getFloat('missing', 9.9));
        $this->assertTrue($bag->getBool('missing', true));
        $this->assertSame('fallback', $bag->getString('missing', 'fallback'));
    }

    public function testArrayAndBagGetters(): void
    {
        $nestedBag = new Bag(['name' => 'Ada']);
        $bag = new Bag([
            'list' => ['one', 'two'],
            'scalar' => 'value',
            'nested' => ['name' => 'Ada'],
            'nested_bag' => $nestedBag,
        ]);
        $defaultBag = new Bag(['default' => true]);
        $defaultBagInterface = $this->createStub(BagInterface::class);

        $this->assertSame(['one', 'two'], $bag->getArray('list'));
        $this->assertSame(['value'], $bag->getArray('scalar'));
        $this->assertSame(['fallback'], $bag->getArray('missing', ['fallback']));
        $this->assertNull($bag->getArray('missing', null));

        $this->assertSame(['name' => 'Ada'], $bag->getBag('nested')->toArray());
        $this->assertSame(['name' => 'Ada'], $bag->getBag('nested_bag')->toArray());
        $this->assertNotSame($nestedBag, $bag->getBag('nested_bag'));
        $this->assertSame(['fallback' => true], $bag->getBag('missing', ['fallback' => true])->toArray());
        $this->assertSame($defaultBag, $bag->getBag('missing', $defaultBag));
        $this->assertSame($defaultBagInterface, $bag->getBag('missing', $defaultBagInterface));
        $this->assertNull($bag->getBag('missing', null));
    }

    public function testDateTimeGetterHandlesInstancesStringsDefaultsAndInvalidValues(): void
    {
        $immutable = new DateTimeImmutable('2026-09-21 10:30:00');
        $default = new DateTimeImmutable('2020-01-01 00:00:00');
        $bag = new Bag([
            'instance' => $immutable,
            'formatted' => '21/09/2026',
            'default_format' => '2026-09-21 10:30:00',
            'invalid' => 'not a date',
            'number' => 123,
        ]);

        $this->assertSame($immutable, $bag->getDateTime('instance'));
        $this->assertInstanceOf(DateTime::class, $bag->getDateTime('formatted', 'd/m/Y'));
        $this->assertSame('2026-09-21', $bag->getDateTime('formatted', 'd/m/Y')->format('Y-m-d'));
        $this->assertSame('2026-09-21 10:30:00', $bag->getDateTime('default_format', null)->format('Y-m-d H:i:s'));
        $this->assertSame($default, $bag->getDateTime('missing', 'Y-m-d', $default));

        $this->expectException(InvalidArgumentException::class);
        $bag->getDateTime('invalid');
    }

    public function testDateTimeGetterRejectsNonDateValues(): void
    {
        $bag = new Bag(['number' => 123]);

        $this->expectException(InvalidArgumentException::class);
        $bag->getDateTime('number');
    }

    public function testDateTimeGetterRejectsDatesWithParsingWarnings(): void
    {
        $bag = new Bag(['invalid_date' => '2026-02-30 10:30:00']);

        $this->expectException(InvalidArgumentException::class);
        $bag->getDateTime('invalid_date');
    }

    public function testJsonGetterDecodesValuesAndReturnsDefaults(): void
    {
        $bag = new Bag([
            'array' => '{"name":"Ada"}',
            'object' => '{"name":"Ada"}',
        ]);

        $this->assertSame(['name' => 'Ada'], $bag->getJson('array'));
        $this->assertInstanceOf(stdClass::class, $bag->getJson('object', null, false));
        $this->assertSame('Ada', $bag->getJson('object', null, false)->name);
        $this->assertSame(['fallback' => true], $bag->getJson('missing', ['fallback' => true]));
    }

    public function testJsonGetterRejectsInvalidAndNonStringValues(): void
    {
        $bag = new Bag(['invalid' => '{', 'number' => 123]);

        try {
            $bag->getJson('invalid');
            $this->fail('Expected invalid JSON to throw an exception.');
        } catch (JsonException) {
        }

        $this->expectException(InvalidArgumentException::class);
        $bag->getJson('number');
    }

    public function testEnumGetterHandlesValuesDefaultsAndMissingEnumClasses(): void
    {
        $bag = new Bag(['status' => TestEnum::Foo->value, 'nullable' => null]);

        $this->assertSame(TestEnum::Foo, $bag->getEnum('status', TestEnum::class));
        $this->assertSame(TestEnum::Default, $bag->getEnum('missing', TestEnum::class, TestEnum::Default));
        $this->assertNull($bag->getEnum('missing', TestEnum::class));
        $this->assertSame(TestEnum::Default, $bag->getEnum('nullable', TestEnum::class, TestEnum::Default));

        $this->expectException(EnumNotFoundException::class);
        $this->expectExceptionMessage('The name "MissingEnum" does not identify a backed enum.');
        $bag->getEnum('status', 'MissingEnum');
    }

    public function testEnumGetterRejectsUnbackedEnums(): void
    {
        $bag = new Bag(['status' => 'value']);

        $this->expectException(EnumNotFoundException::class);
        $this->expectExceptionMessage(
            sprintf('The name "%s" does not identify a backed enum.', UnbackedTestEnum::class)
        );
        $bag->getEnum('status', UnbackedTestEnum::class);
    }

    public function testDotNotationResolvesNestedValuesAndPrefersExactKeys(): void
    {
        $bag = new Bag([
            'user' => [
                'profile' => [
                    'name' => 'Ada',
                    'active' => true,
                    'nullable' => null,
                ],
            ],
            'user.profile.name' => 'Exact key',
            'scalar' => 'value',
        ]);

        $this->assertSame('Exact key', $bag->getString('user.profile.name'));
        $this->assertTrue($bag->has('user.profile.active'));
        $this->assertTrue($bag->isSet('user.profile.active'));
        $this->assertTrue($bag->getBool('user.profile.active'));
        $this->assertTrue($bag->has('user.profile.nullable'));
        $this->assertFalse($bag->isSet('user.profile.nullable'));
        $this->assertSame('fallback', $bag->get('user.profile.nullable', 'fallback'));
        $this->assertSame('fallback', $bag->get('user.profile.missing', 'fallback'));
        $this->assertSame('fallback', $bag->get('scalar.child', 'fallback'));
        $this->assertFalse($bag->has('user.profile.missing'));
    }

    public function testRemovingExactAndNestedKeys(): void
    {
        $bag = new Bag([
            'user.profile.name' => 'Exact key',
            'user' => [
                'profile' => [
                    'name' => 'Nested value',
                ],
            ],
        ]);

        $this->assertSame($bag, $bag->remove('user.profile.name'));
        $this->assertSame('Nested value', $bag->getString('user.profile.name'));
        $bag->remove('user.profile.name');
        $this->assertFalse($bag->has('user.profile.name'));
    }

    public function testRemovingMissingKeysThrowsAnException(): void
    {
        $bag = new Bag(['scalar' => 'value', 'array' => []]);

        try {
            $bag->remove('scalar.child');
            $this->fail('Expected a missing intermediate path to throw an exception.');
        } catch (ElementNotFoundException) {
        }

        $this->expectException(ElementNotFoundException::class);
        $bag->remove('array.child');
    }

    public function testEmptyStateAndClear(): void
    {
        $bag = new Bag();

        $this->assertTrue($bag->isEmpty());
        $this->assertFalse($bag->isNotEmpty());
        $this->assertSame($bag, $bag->add('value', true));
        $this->assertFalse($bag->isEmpty());
        $this->assertTrue($bag->isNotEmpty());
        $this->assertSame($bag, $bag->clear());
        $this->assertTrue($bag->isEmpty());
    }

    public function testCollectionOperationsReturnExpectedBagsAndBooleans(): void
    {
        $bag = new Bag(['ada' => 100, 'grace' => 95, 'linus' => 80]);

        $filtered = $bag->filter(fn (string $name, int $score): bool => $score >= 95);
        $mapped = $filtered->map(fn (string $name, int $score): string => "$name:$score");

        $this->assertSame(['ada' => 100, 'grace' => 95], $filtered->toArray());
        $this->assertSame(['ada' => 'ada:100', 'grace' => 'grace:95'], $mapped->toArray());
        $this->assertTrue($bag->every(fn (string $name, int $score): bool => $score >= 80));
        $this->assertFalse($bag->every(fn (string $name, int $score): bool => $score >= 95));
        $this->assertTrue($bag->some(fn (string $name, int $score): bool => $score === 100));
        $this->assertFalse($bag->some(fn (string $name, int $score): bool => $score === 70));
    }

    public function testDotNotationFlattensNestedArraysAndPreservesEmptyArrays(): void
    {
        $bag = new Bag([
            'user' => [
                'name' => 'Ada',
                'address' => ['city' => 'London'],
            ],
            'tags' => [],
        ]);

        $this->assertSame($bag, $bag->toDotNotation());
        $this->assertSame([
            'user.name' => 'Ada',
            'user.address.city' => 'London',
            'tags' => [],
        ], $bag->toArray());
    }

    public function testDotNotationPrefersExactDottedKeysRegardlessOfInputOrder(): void
    {
        $bags = [
            new Bag([
                'user.name' => 'Exact value',
                'user' => ['name' => 'Nested value'],
            ]),
            new Bag([
                'user' => ['name' => 'Nested value'],
                'user.name' => 'Exact value',
            ]),
        ];

        foreach ($bags as $bag) {
            $this->assertSame(['user.name' => 'Exact value'], $bag->toDotNotation()->toArray());
        }
    }

    public function testToJsonEncodesBagContents(): void
    {
        $bag = new Bag(['name' => 'Ada', 'active' => true]);

        $this->assertSame('{"name":"Ada","active":true}', $bag->toJson());
    }
}

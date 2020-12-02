<?php
/**
 * Copyright 2017-2019 Milos Jovanovic <phplibs@yomy.work>
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
declare(strict_types=1);

namespace YomY\DataStructures\Tests\ValueObject;

use YomY\DataStructures\Tests\ValueObject\Helper\EnumValueObjectExample;
use YomY\DataStructures\ValueObject\ValueObject;

class EnumValueObjectTest extends \PHPUnit\Framework\TestCase {

    /**
     * @throws \ReflectionException
     */
    public function setUp() {
        $valueObject = ValueObject::instance(null);
        $reflection = new \ReflectionClass($valueObject);
        $instancesProperty = $reflection->getProperty('instances');
        $instancesProperty->setAccessible(true);
        $instancesProperty->setValue(null, null);
    }

    /**
     * Provides valid values for a specific enum value object
     *
     * @return array
     */
    public static function ValueProvider(): array {
        return [
            [EnumValueObjectExample::ENUM1],
            [EnumValueObjectExample::ENUM2],
            [EnumValueObjectExample::ENUM3],
            [EnumValueObjectExample::ENUM4]
        ];
    }

    /**
     * Test creating a valid enum value object
     *
     * @dataProvider ValueProvider
     * @param mixed $value
     */
    public function testInstance($value) {
        $object = EnumValueObjectExample::instance($value);
        self::assertEquals($value, $object->value());
    }

    /**
     * Test creating the enum object with invalid value fails
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidValue() {
        EnumValueObjectExample::instance('invalidValue');
    }

    /**
     * Test instantiating an object with a magic method (by constant name) gives the same object as instance method
     */
    public function testMagicMethod() {
        $object1 = EnumValueObjectExample::instance(EnumValueObjectExample::ENUM3);
        $object2 = EnumValueObjectExample::ENUM3();
        self::assertSame($object1, $object2);
    }

    /**
     * Tests isValid method for valid keys
     *
     * @dataProvider ValueProvider
     * @param mixed $value
     */
    public function testKeyValid($value) {
        $valid = EnumValueObjectExample::isValid($value);
        self::assertTrue($valid);
    }

    /**
     * Tests isValid method for invalid key
     */
    public function testKeyInvalid() {
        $valid = EnumValueObjectExample::isValid('InvalidValue');
        self::assertFalse($valid);
    }

    /**
     * Test getAvailableValues method
     *
     * @throws \ReflectionException
     */
    public function testGetAvailableValues() {
        $class = EnumValueObjectExample::class;
        $reflection = new \ReflectionClass($class);
        $expectedValues = $reflection->getConstants();
        $values = EnumValueObjectExample::getAvailableValues();
        self::assertEquals($expectedValues, $values);
    }

    /**
     * Tests that no instance of wrong type of value can be created
     *
     * @expectedException \InvalidArgumentException
     */
    public function testWrongTypeInstance() {
        EnumValueObjectExample::instance(2);
    }

    /**
     * Tests array contain
     */
    public function testInArray() {
        $array = [EnumValueObjectExample::ENUM1(), EnumValueObjectExample::ENUM3()];
        self::assertContains(EnumValueObjectExample::ENUM1(), $array, '', false, true, true);
        self::assertNotContains(EnumValueObjectExample::ENUM2(), $array, '', false, true, true);
        self::assertContains(EnumValueObjectExample::ENUM3(), $array, '', false, true, true);
        self::assertNotContains(EnumValueObjectExample::ENUM4(), $array, '', false, true, true);
        self::assertContains(EnumValueObjectExample::ENUM1(), $array, '', false, false, false);
        self::assertNotContains(EnumValueObjectExample::ENUM2(), $array, '', false, false, false);
        self::assertContains(EnumValueObjectExample::ENUM3(), $array, '', false, false, false);
        self::assertNotContains(EnumValueObjectExample::ENUM4(), $array, '', false, false, false);
    }

}
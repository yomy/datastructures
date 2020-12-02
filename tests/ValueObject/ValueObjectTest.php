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

use YomY\DataStructures\Tests\ValueObject\Helper\ValueObjectExample;
use YomY\DataStructures\Tests\ValueObject\Helper\ValueObjectExampleIsolated;
use YomY\DataStructures\Tests\ValueObject\Helper\ValueObjectExampleLevel2;
use YomY\DataStructures\Tests\ValueObject\Helper\ValueObjectExampleLevel3;
use YomY\DataStructures\Tests\ValueObject\Helper\WeakValueObjectExample;
use YomY\DataStructures\ValueObject\ValueObject;

class ValueObjectTest extends \PHPUnit\Framework\TestCase {

    /**
     * @throws \ReflectionException
     */
    public function setUp() {
        $this->resetValueObjectInstances();
    }

    /**
     * @throws \ReflectionException
     */
    private function resetValueObjectInstances() {
        $valueObject = ValueObject::instance(null);
        $reflection = new \ReflectionClass($valueObject);
        $instancesProperty = $reflection->getProperty('instances');
        $instancesProperty->setAccessible(true);
        $instancesProperty->setValue(null, null);
    }

    /**
     * Provides valid values for a value object
     *
     * @return array
     */
    public static function ValueProvider(): array {
        return [
            [1],
            ['1'],
            ['string with spaces'],
            [true],
            [false],
            [null],
            ['0'],
            [''],
            [\PHP_INT_MAX],
            [\PHP_INT_MIN],
            [(string)\PHP_INT_MAX],
            [(string)\PHP_INT_MIN]
        ];
    }

    /**
     * Provides invalid values for a value object
     *
     * @return array
     */
    public static function InvalidValueProvider(): array {
        return [
            [[]],
            [[1]],
            [(object)[]],
            [(object)[1]]
        ];
    }

    /**
     * Provides comparison values that should evaluate as different
     *
     * @return array
     */
    public static function DifferenceValueProvider(): array {
        return [
            [0, '0'],
            [0, ''],
            [1, '1'],
            [-1, '-1'],
            [true, 1],
            [true, -1],
            [true, '1'],
            [true, '-1'],
            [true, 'true'],
            [false, ''],
            [false, 0],
            [false, '0'],
            [false, 'false'],
            [null, '0'],
            [null, 'null'],
            [null, ''],
            [null, false],
        ];
    }

    /**
     * Test creating a value object
     *
     * @dataProvider ValueProvider
     * @param mixed $value
     */
    public function testInstance($value) {
        $object = ValueObject::instance($value);
        self::assertEquals($value, $object->value());
    }

    /**
     * Test if creating a value object with invalid value fails
     *
     * @dataProvider InvalidValueProvider
     * @expectedException \InvalidArgumentException
     * @param mixed $value
     */
    public function testInstanceInvalidValue($value) {
        ValueObject::instance($value);
    }

    /**
     * Test creating a value object from extending class
     *
     * @dataProvider ValueProvider
     * @param mixed $value
     */
    public function testExtendedInstance($value) {
        $object = ValueObjectExample::instance($value);
        self::assertEquals($value, $object->value());
    }

    /**
     * Test creating a value object from extending class fails with invalid value
     *
     * @dataProvider InvalidValueProvider
     * @expectedException \InvalidArgumentException
     * @param mixed $value
     */
    public function testExtendedInstanceInvalidValue($value) {
        ValueObjectExample::instance($value);
    }

    /**
     * Test if multiple calls to instance method with same value produce the same object
     *
     * @dataProvider ValueProvider
     * @param mixed $value
     */
    public function testCompareObjectsSame($value) {
        $object1 = ValueObject::instance($value);
        $object2 = ValueObject::instance($value);
        self::assertSame($object1, $object2);
        self::assertTrue($object1->equals($object2));
        self::assertTrue($object2->equals($object1));
    }

    /**
     * Test if multiple calls to instance method with different values produce different objects
     *
     * @dataProvider DifferenceValueProvider
     * @param mixed $value1
     * @param mixed $value2
     */
    public function testCompareObjectsNotSame($value1, $value2) {
        $object1 = ValueObject::instance($value1);
        $object2 = ValueObject::instance($value2);
        self::assertNotSame($object1, $object2);
        self::assertNotEquals($object1, $object2);
        self::assertFalse($object1->equals($object2));
        self::assertFalse($object2->equals($object1));
    }

    /**
     * Test if instances of different classes with the same value are not the same objects
     */
    public function testCompareObjectsNotSameDifferentClass() {
        $object1 = ValueObject::instance(1);
        $object2 = ValueObjectExample::instance(1);
        self::assertNotSame($object1, $object2);
        self::assertNotEquals($object1, $object2);
        self::assertFalse($object1->equals($object2));
        self::assertFalse($object2->equals($object1));
    }


    /**
     * Test if instances of same extended class with the same value are not the same objects
     *
     * @dataProvider DifferenceValueProvider
     * @param mixed $value1
     * @param mixed $value2
     */
    public function testCompareObjectsNotSameExtendedClass($value1, $value2) {
        $object1 = ValueObjectExample::instance($value1);
        $object2 = ValueObjectExample::instance($value2);
        self::assertNotSame($object1, $object2);
        self::assertNotEquals($object1, $object2);
        self::assertFalse($object1->equals($object2));
        self::assertFalse($object2->equals($object1));
    }

    /**
     * Test if instances of different classes with the same value are not the same objects (level 2 class)
     */
    public function testCompareObjectsNotSameDifferentClassLevel2() {
        $object1 = ValueObject::instance(1);
        $object2 = ValueObjectExampleLevel2::instance(1);
        self::assertNotSame($object1, $object2);
        self::assertNotEquals($object1, $object2);
        self::assertFalse($object1->equals($object2));
        self::assertFalse($object2->equals($object1));
    }

    /**
     * Test if instances of different classes with the same value are not the same objects (level 3 class)
     */
    public function testCompareObjectsNotSameDifferentClassLevel3() {
        $object1 = ValueObject::instance(1);
        $object2 = ValueObjectExampleLevel3::instance(1);
        self::assertNotSame($object1, $object2);
        self::assertNotEquals($object1, $object2);
        self::assertFalse($object1->equals($object2));
        self::assertFalse($object2->equals($object1));
    }

    /**
     * Test if instances of different classes with the same value are not the same objects (deep level classes)
     */
    public function testCompareObjectsNotSameDifferentClassDeepLevelClasses() {
        $object1 = ValueObjectExampleLevel2::instance(1);
        $object2 = ValueObjectExampleLevel3::instance(1);
        self::assertNotSame($object1, $object2);
        self::assertNotEquals($object1, $object2);
        self::assertFalse($object1->equals($object2));
        self::assertFalse($object2->equals($object1));
    }

    /**
     * Tests that if factory is reset the objects of the same value are not the same
     * @throws \ReflectionException
     */
    public function testObjectsCompareWhenInternallyReset() {
        $object1 = ValueObject::instance(1);
        $this->resetValueObjectInstances();
        $object2 = ValueObject::instance(1);
        self::assertNotSame($object1, $object2);
        self::assertEquals($object1, $object2);
        self::assertFalse($object1->equals($object2));
        self::assertFalse($object2->equals($object1));
    }

    /**
     * Tests that if factory is reset the weak objects of the same value are the same with internal compare
     * @throws \ReflectionException
     */
    public function testWeakObjectsCompareWhenInternallyReset() {
        $object1 = WeakValueObjectExample::instance(1);
        $this->resetValueObjectInstances();
        $object2 = WeakValueObjectExample::instance(1);
        self::assertNotSame($object1, $object2);
        self::assertEquals($object1, $object2);
        self::assertTrue($object1->equals($object2));
        self::assertTrue($object2->equals($object1));
    }

    /**
     * Tests comparing weak and non weak value objects with the same value
     */
    public function testWeakValueObjectCompareToNonWeakValueObjectWithSameValue() {
        $object1 = WeakValueObjectExample::instance(1);
        $object2 = ValueObject::instance(1);
        self::assertNotSame($object1, $object2);
        self::assertNotEquals($object1, $object2);
        self::assertFalse($object1->equals($object2));
        self::assertFalse($object2->equals($object1));
    }

    /**
     * Tests with reflection if internal storage is isolated on a value object
     * @throws \ReflectionException
     */
    public function testIsolatedStorage() {
        $object1 = ValueObjectExample::instance(1);
        $object2 = ValueObjectExampleIsolated::instance(1);
        $object1Reflection = new \ReflectionClass($object1);
        $object1InstancesProperty = $object1Reflection->getProperty('instances');
        $object1InstancesProperty->setAccessible(true);
        $object1StorageValue = $object1InstancesProperty->getValue();
        $object2Reflection = new \ReflectionClass($object2);
        $object2InstancesProperty = $object2Reflection->getProperty('instances');
        $object2InstancesProperty->setAccessible(true);
        $object2StorageValue = $object2InstancesProperty->getValue();
        self::assertNotEquals($object1StorageValue, $object2StorageValue);
        self::assertCount(1, $object1StorageValue);
        self::assertCount(1, $object2StorageValue);
    }

    /**
     * Tests access to non-existing property fails
     *
     * @expectedException \BadFunctionCallException
     */
    public function testPropertyAccessFail() {
        $object = ValueObject::instance(1);
        $object->property;
    }

    /**
     * Tests setting a non-existing property fails
     *
     * @expectedException \BadFunctionCallException
     */
    public function testPropertySetFail() {
        $object = ValueObject::instance(1);
        $object->property = 1;
    }

    /**
     * Tests if isset fails
     *
     * @expectedException \BadFunctionCallException
     */
    public function testPropertyIssetFail() {
        $object = ValueObject::instance(1);
        isset($object->property);
    }

    /**
     * Tests if cloning fails
     *
     * @expectedException \BadFunctionCallException
     */
    public function testCloneFail() {
        $object = ValueObject::instance(1);
        clone $object;
    }

    /**
     * @expectedException \BadFunctionCallException
     */
    public function testUnserializeFail() {
        $object = ValueObject::instance(1);
        $serialized = \serialize($object);
        \unserialize($serialized);
    }

    /**
     * Test that unserialize works on weak value object
     */
    public function testWeakObjectUnserializePass() {
        $object = WeakValueObjectExample::instance(1);
        $serialized = \serialize($object);
        $unserialized = \unserialize($serialized);
        self::assertEquals($object, $unserialized);
    }

}
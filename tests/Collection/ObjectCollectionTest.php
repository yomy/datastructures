<?php
/**
 * Copyright 2018-2019 Milos Jovanovic <phplibs@yomy.work>
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

namespace YomY\DataStructures\Tests\Collection;

use YomY\DataStructures\Collection\ObjectCollection;
use YomY\DataStructures\Tests\Collection\Helper\ExampleObject1;
use YomY\DataStructures\Tests\Collection\Helper\ExampleObject1Collection;
use YomY\DataStructures\Tests\Collection\Helper\ExampleObject1Extended;
use YomY\DataStructures\Tests\Collection\Helper\ExampleObject2;
use YomY\DataStructures\Tests\Collection\Helper\ExampleInterface;
use YomY\DataStructures\Tests\Collection\Helper\ExampleObjectExtendingInterface;
use YomY\DataStructures\Tests\Collection\Helper\ValueObject1;
use YomY\DataStructures\Tests\Collection\Helper\ValueObject1Collection;
use YomY\DataStructures\Tests\Collection\Helper\ValueObject2;

require_once 'Helper/ExampleObject1.php';
require_once 'Helper/ExampleObject2.php';
require_once 'Helper/ExampleObject1Extended.php';
require_once 'Helper/ExampleObject1Collection.php';
require_once 'Helper/ExampleInterface.php';
require_once 'Helper/ExampleObjectExtendingInterface.php';
require_once 'Helper/AbstractValueObject.php';
require_once 'Helper/ValueObject1.php';
require_once 'Helper/ValueObject2.php';
require_once 'Helper/ValueObject1Collection.php';

/**
 * @package YomY\DataStructures\Tests\Collection
 */
class ObjectCollectionTest extends \PHPUnit\Framework\TestCase {

    /**
     * Test adding an object
     */
    public function testAdd() {
        $object = new ExampleObject1();
        $collection = new ObjectCollection(ExampleObject1::class);
        $collection->add($object);
        $result = $collection->getAll();
        $first = reset($result);
        self::assertSame($object, $first);
    }

    /**
     * Test adding an object that extends interface
     */
    public function testAddInterface() {
        $object = new ExampleObjectExtendingInterface();
        $collection = new ObjectCollection(ExampleInterface::class);
        $collection->add($object);
        $result = $collection->getAll();
        $first = reset($result);
        self::assertSame($object, $first);
    }

    /**
     * Test adding an object multiple times
     */
    public function testAddMultipleOfSame() {
        $object = new ExampleObject1();
        $collection = new ObjectCollection(ExampleObject1::class);
        $collection->add($object);
        $collection->add($object);
        $result = $collection->getAll();
        self::assertCount(2, $result);
        self::assertSame($object, $result[0]);
        self::assertSame($object, $result[1]);
    }

    /**
     * Test adding different objects
     */
    public function testAddMultipleOfDifferent() {
        $object1 = new ExampleObject1();
        $object2 = new ExampleObject1();
        $collection = new ObjectCollection(ExampleObject1::class);
        $collection->add($object1);
        $collection->add($object2);
        $result = $collection->getAll();
        self::assertCount(2, $result);
        self::assertSame($object1, $result[0]);
        self::assertSame($object2, $result[1]);
    }

    /**
     * Test adding an extended object
     */
    public function testAddExtendedObjects() {
        $object1 = new ExampleObject1();
        $object2 = new ExampleObject1Extended();
        $collection = new ObjectCollection(ExampleObject1::class);
        $collection->add($object1);
        $collection->add($object2);
        $result = $collection->getAll();
        self::assertCount(2, $result);
        self::assertSame($object1, $result[0]);
        self::assertSame($object2, $result[1]);
    }

    /**
     * Test adding an object of wrong type
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAddFailWrongObject() {
        $collection = new ObjectCollection(ExampleObject1::class);
        $collection->add(new ExampleObject2());
    }

    /**
     * Test adding an object of type that doesn't extend the correct interface
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAddFailWrongObjectInterface() {
        $collection = new ObjectCollection(ExampleInterface::class);
        $collection->add(new ExampleObject1());
    }

    /**
     * Test adding an object of wrong type from array
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAddArrayFailWrongObject() {
        $collection = new ObjectCollection(ExampleObject1::class);
        $collection->addArray([new ExampleObject2()]);
    }

    /**
     * Test adding an object of wrong type from array
     * when it contains a correct object as well
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAddArrayFailWrongObjectWithinArray() {
        $inputArray = [new ExampleObject1(), new ExampleObject2()];
        $collection = new ObjectCollection(ExampleObject1::class);
        $collection->addArray($inputArray);
    }

    /**
     * Test adding an object of wrong type from array
     * when it contains a correct object as well
     * will add that correct object
     */
    public function testAddArrayWithWrongObjectAddsObjectsBeforeFail() {
        $inputArray = [
            0 => new ExampleObject1(),
            1 => new ExampleObject2(),
            2 => new ExampleObject1(),
            3 => new ExampleObject2(),
            4 => new ExampleObject1()
        ];
        $collection = new ObjectCollection(ExampleObject1::class);
        $caught = false;
        try {
            $collection->addArray($inputArray);
        } catch (\Exception $e) {
            $caught = true;
        }
        $result = $collection->getAll();
        $first = reset($result);
        self::assertTrue($caught);
        self::assertEquals(1, $collection->count());
        self::assertSame($inputArray[0], $first);
    }

    /**
     * Test adding an array of both right and wrong objects
     * with succeeding in all right objects
     */
    public function testTryAddArrayWithWrongObjectAddsOtherObjects() {
        $inputArray = [
            0 => new ExampleObject1(),
            1 => new ExampleObject2(),
            2 => new ExampleObject1(),
            3 => new ExampleObject2(),
            4 => new ExampleObject1()
        ];
        $expectedAdded = [$inputArray[0], $inputArray[2], $inputArray[4]];
        $expectedFailedToAdd = [$inputArray[1], $inputArray[3]];
        $collection = new ObjectCollection(ExampleObject1::class);
        $failedToAdd = $collection->tryAddArray($inputArray);
        $added = $collection->getAll();
        self::assertEquals(\count($expectedAdded), $collection->count());
        self::assertSame($expectedAdded, $added);
        self::assertSame($expectedFailedToAdd, $failedToAdd);
    }

    /**
     * Test adding an object to a named collection
     */
    public function testNamedAdd() {
        $object = new ExampleObject1();
        $collection = new ExampleObject1Collection();
        $collection->add($object);
        $result = $collection->getAll();
        $first = reset($result);
        self::assertSame($object, $first);
    }

    /**
     * Test adding an extended object to a named collection
     */
    public function testNamedAddExtended() {
        $object = new ExampleObject1Extended();
        $collection = new ExampleObject1Collection();
        $collection->add($object);
        $result = $collection->getAll();
        $first = reset($result);
        self::assertSame($object, $first);
    }

    /**
     * Test adding wrong object to a named collection
     *
     * @expectedException \InvalidArgumentException
     */
    public function testNamedAddFailWrongObject() {
        $object = new ExampleObject2();
        $collection = new ExampleObject1Collection();
        $collection->add($object);
    }

    /**
     * Test transforming with a wrong object
     *
     * @expectedException \InvalidArgumentException
     */
    public function testFailTransformToOtherCollection() {
        $object1Collection = new ObjectCollection(ExampleObject1::class);
        $object2Collection = new ObjectCollection(ExampleObject2::class);
        $object1Collection->add(new ExampleObject1());
        $object1Collection->transformToCollection($object2Collection);
    }

    /**
     * Test transforming with a correct object
     */
    public function testTransformToOtherCollection() {
        $object1Collection = new ObjectCollection(ValueObject1::class);
        $object2Collection = new ObjectCollection(ValueObject2::class);
        $inputObject = new ValueObject1(42);
        $object1Collection->add($inputObject);
        $object1Collection->transformToCollection(
            $object2Collection,
            function(ValueObject1 $object) {
                return new ValueObject2($object->getValue()); //For testing purposes. Should be based on ExampleObject1
            }
        );
        self::assertCount(1, $object2Collection);
        $items = $object2Collection->getAll();
        $firstItem = reset($items);
        self::assertEquals($inputObject->getValue(), $firstItem->getValue());
    }

    /**
     * Test transforming to a provided class string
     */
    public function testTransformToOtherCollectionClass() {
        $sourceCollection = new ObjectCollection(ValueObject2::class);
        $inputObject = new ValueObject2(42);
        $sourceCollection->add($inputObject);
        $transformedCollection = $sourceCollection->transformToCollectionClass(
            ValueObject1Collection::class,
            function(ValueObject2 $object) {
                return new ValueObject1($object->getValue()); //For testing purposes. Should be based on ExampleObject1
            }
        );
        self::assertCount(1, $transformedCollection);
        $items = $sourceCollection->getAll();
        $firstItem = reset($items);
        self::assertEquals($inputObject->getValue(), $firstItem->getValue());
    }

}
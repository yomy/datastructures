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

use YomY\DataStructures\Collection\GenericCollection;
use YomY\DataStructures\Tests\Collection\Helper\ExtendedGenericCollection;

require_once 'Helper/ExtendedGenericCollection.php';

/**
 * @package YomY\DataStructures\Tests\Collection
 */
class GenericCollectionTest extends \PHPUnit\Framework\TestCase {

    /**
     * Provides values for collection
     *
     * @return array
     */
    public static function ValueProvider(): array {
        return [
            [[1]],
            [['string']],
            [[null]],
            [[0]],
            [['0']],
            [[false]],
            [[true]],
            [['false']],
            [['']],
            [[[1, 2]]],
            [[(object)[1, 2]]],
            [[[]]],
            [[(object)[]]],
            [[1, 2]],
            [['string1', 'string2']],
            [['same', 'same']],
            [['', '']],
            [[0, 0]],
            [[null, null]],
            [[(object)[1, 2], (object)[3, 4]]],
            [[1, '2', null, false, true, '0', '', 'string']]
        ];
    }

    /**
     * Provides values for testing removal functionality
     *
     * @return array
     */
    public static function RemoveValueProvider(): array {
        return [
            [[1, 2, 3], 2, [1, 3]],
            [[2, 1, 2, 2, 3, 2, 4, 5, 2], 2, [1, 3, 4, 5]],
            [[], 2, []],
            [[2], 2, []],
            [[2, 2], 2, []],
            [[1, 3], 2, [1, 3]]
        ];
    }

    /**
     * Test creation of the collection giving one item at a time
     *
     * @dataProvider ValueProvider
     * @param array $inputArray
     */
    public function testBuildingPerItem(array $inputArray) {
        $collection = new GenericCollection();
        foreach ($inputArray as $item) {
            $collection->add($item);
        }
        $items = $collection->getAll();
        self::assertEquals($inputArray, $items);
    }

    /**
     * Test creation of the collection giving an array of items
     *
     * @dataProvider ValueProvider
     * @param array $inputArray
     */
    public function testBuildingFromArray(array $inputArray) {
        $collection = new GenericCollection();
        $collection->addArray($inputArray);
        $items = $collection->getAll();
        self::assertEquals($inputArray, $items);
    }

    /**
     * Tests count method
     *
     * @dataProvider ValueProvider
     * @param array $inputArray
     */
    public function testCount(array $inputArray) {
        $expectedCount = \count($inputArray);
        $collection = new GenericCollection();
        $collection->addArray($inputArray);
        self::assertEquals($expectedCount, $collection->count());
    }

    /**
     * Test count on empty collection
     */
    public function testCountEmpty() {
        $collection = new GenericCollection();
        self::assertEquals(0, $collection->count());
    }

    /**
     * Test clear method
     */
    public function testClear() {
        $collection = new GenericCollection();
        $collection->add(1);
        $collection->clear();
        self::assertEquals([], $collection->getAll());
    }

    /**
     * Test isEmpty method on empty collection
     */
    public function testIsEmptyOnEmpty() {
        $collection = new GenericCollection();
        self::assertTrue($collection->isEmpty());
    }

    /**
     * Test isEmpty method on non empty collection
     */
    public function testIsEmptyOnNonEmpty() {
        $collection = new GenericCollection();
        $collection->add(1);
        self::assertFalse($collection->isEmpty());
    }

    /**
     * Test isEmpty method when collection has been cleared
     */
    public function testIsEmptyAfterClear() {
        $collection = new GenericCollection();
        $collection->add(1);
        $collection->clear();
        self::assertTrue($collection->isEmpty());
    }

    /**
     * Tests contains function
     *
     * @dataProvider ValueProvider
     * @param array $inputArray
     */
    public function testContainsValue(array $inputArray) {
        $collection = new GenericCollection();
        $collection->addArray($inputArray);
        foreach ($inputArray as $item) {
            self::assertTrue($collection->contains($item));
        }
    }

    /**
     * Tests contains function when value doesn't exist
     *
     * @dataProvider ValueProvider
     * @param array $inputArray
     */
    public function testContainsEmpty(array $inputArray) {
        $collection = new GenericCollection();
        foreach ($inputArray as $item) {
            self::assertFalse($collection->contains($item));
        }
    }

    /**
     * Tests removing items
     *
     * @dataProvider RemoveValueProvider
     * @param array $inputArray
     * @param mixed $remove
     * @param array $expectedArray
     */
    public function testRemove($inputArray, $remove, $expectedArray) {
        $collection = new GenericCollection();
        $collection->addArray($inputArray);
        $collection->remove($remove);
        self::assertEquals($expectedArray, array_values($collection->getAll()));
    }

    /**
     * Tests copying a collection
     *
     * @dataProvider ValueProvider
     * @param array $inputArray
     */
    public function testCopy(array $inputArray) {
        $collection = new GenericCollection();
        $collection->addArray($inputArray);
        $copy = $collection->copy();
        self::assertSame($collection->getAll(), $copy->getAll());
        self::assertSame($collection->count(), $copy->count());
        self::assertSame($collection->isEmpty(), $copy->isEmpty());
    }

    /**
     * Tests copying an extended collection
     *
     * @dataProvider ValueProvider
     * @param array $inputArray
     */
    public function testCopyExtended(array $inputArray) {
        $collection = new ExtendedGenericCollection();
        $collection->addArray($inputArray);
        $copy = $collection->copy();
        self::assertInstanceOf(ExtendedGenericCollection::class, $copy);
        self::assertSame($collection->getAll(), $copy->getAll());
        self::assertSame($collection->count(), $copy->count());
        self::assertSame($collection->isEmpty(), $copy->isEmpty());
    }

    /**
     * Tests if copy doesn't update if original does
     *
     * @dataProvider ValueProvider
     * @param array $inputArray
     */
    public function testCopyNotModifiedWithOriginal(array $inputArray) {
        $collection = new GenericCollection();
        $collection->addArray($inputArray);
        $copy = $collection->copy();
        $itemsInCopy = $copy->getAll();
        $countInCopy = $copy->count();
        $collection->addArray($inputArray);
        $itemsInCopyAfter = $copy->getAll();
        $countInCopyAfter = $copy->count();
        self::assertNotEquals($collection->getAll(), $itemsInCopy);
        self::assertNotEquals($collection->count(), $countInCopy);
        self::assertSame($itemsInCopy, $itemsInCopyAfter);
        self::assertSame($countInCopy, $countInCopyAfter);
    }

    /**
     * Tests if original doesn't update if copy does
     *
     * @dataProvider ValueProvider
     * @param array $inputArray
     */
    public function testOriginalNotModifiedWithCopy(array $inputArray) {
        $collection = new GenericCollection();
        $collection->addArray($inputArray);
        $itemsInOriginal = $collection->getAll();
        $countInOriginal = $collection->count();
        $copy = $collection->copy();
        $copy->addArray($inputArray);
        $itemsInOriginalAfter = $collection->getAll();
        $countInOriginalAfter = $collection->count();
        self::assertNotEquals($collection->getAll(), $copy->getAll());
        self::assertNotEquals($collection->count(), $copy->count());
        self::assertSame($itemsInOriginal, $itemsInOriginalAfter);
        self::assertSame($countInOriginal, $countInOriginalAfter);
    }

    /**
     * Tests copyEmpty returns an empty copy
     */
    public function testCopyEmpty() {
        $collection = new GenericCollection();
        $collection->add(1);
        $copy = $collection->copyEmpty();
        self::assertEquals(0, $copy->count());
        self::assertEquals([], $copy->getAll());
    }

    /**
     * Tests copyEmpty returns an empty copy
     */
    public function testExtendedCopyEmpty() {
        $collection = new ExtendedGenericCollection();
        $collection->add(1);
        $copy = $collection->copyEmpty();
        self::assertInstanceOf(ExtendedGenericCollection::class, $copy);
        self::assertEquals(0, $copy->count());
        self::assertEquals([], $copy->getAll());
    }

    /**
     * Tests basic sorting with a callback
     */
    public function testBasicSort() {
        $inputArray = [9, 8, 7, 6, 5, 4, 3, 2, 1];
        $expectedArray = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $collection = new GenericCollection();
        $collection->addArray($inputArray);
        $collection->sort(function($object1, $object2) {
            return $object1 > $object2;
        });
        self::assertEquals($expectedArray, $collection->getAll());
    }

    /**
     * Tests basic filtering with a callback
     */
    public function testBasicFilter() {
        $inputArray = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $expectedFiltered = [6, 7, 8, 9];
        $collection = new GenericCollection();
        $collection->addArray($inputArray);
        $filtered = $collection->filter(function($object) {
            return $object > 5;
        });
        self::assertEquals($inputArray, $collection->getAll());
        self::assertEquals($expectedFiltered, $filtered->getAll());
    }

    /**
     * Tests basic filtering with a callback on extended collection
     */
    public function testBasicFilterExtended() {
        $inputArray = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $expectedFiltered = [6, 7, 8, 9];
        $collection = new ExtendedGenericCollection();
        $collection->addArray($inputArray);
        $filtered = $collection->filter(function($object) {
            return $object > 5;
        });
        self::assertInstanceOf(ExtendedGenericCollection::class, $filtered);
        self::assertEquals($inputArray, $collection->getAll());
        self::assertEquals($expectedFiltered, $filtered->getAll());
    }

    /**
     * Tests filtering which would give an empty result
     */
    public function testFilterEmptyResult() {
        $inputArray = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $collection = new GenericCollection();
        $collection->addArray($inputArray);
        $filtered = $collection->filter(function($object) {
            return $object > 99;
        });
        self::assertEquals($inputArray, $collection->getAll());
        self::assertEquals([], $filtered->getAll());
        self::assertEquals(0, $filtered->count());
    }

    /**
     * Test iteration
     */
    public function testIterate() {
        $inputArray = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $collection = new GenericCollection();
        $collection->addArray($inputArray);
        $count = 0;
        foreach ($collection as $key => $item) {
            self::assertEquals($inputArray[$key], $item);
            $count++;
        }
        self::assertEquals($count, $collection->count());
    }

}
<?php
/**
 * Copyright 2018 Milos Jovanovic <email.yomy@gmail.com>
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

use YomY\DataStructures\Collection\KeyValueCollection;
use YomY\DataStructures\Tests\Collection\Helper\ExampleObject1;
use YomY\DataStructures\Tests\Collection\Helper\ExampleObject2;

require_once 'Helper/ExampleObject1.php';
require_once 'Helper/ExampleObject2.php';

/**
 * @package YomY\DataStructures\Tests\Collection
 */
class KeyValueCollectionTest extends \PHPUnit\Framework\TestCase {

    /**
     * Provides pairs for collection
     *
     * @return array
     */
    public static function PairProvider(): array {
        return [
            [[[1, 2]]],
            [[[1, 2], [3, 4]]],
            [[['key', 'value'], ['key2', 'value2']]],
            [[[null, 1]]],
            [[[false, true]]],
            [[[null, null]]],
            [[[[1, 2], [3, 4]]]],
            [[[[1, 2], [3, 4]], [[5, 6], [7, 8]]]],
            [[[(object)[1, 2], (object)[3, 4]]]]
        ];
    }

    /**
     * Provides pairs for collection
     *
     * @return array
     */
    public static function PairProviderGet(): array {
        $simpleObject = (object)[1];
        return [
            [[[1, 2]], 1, 2],
            [[[1, 2], [3, 4], [5, 6]], 3, 4],
            [[[1, 2]], 3, null],
            [[[1, null]], 1, null],
            [[[null, 1]], null, 1],
            [[[[1], 1]], [1], 1],
            [[[$simpleObject, 1]], $simpleObject, 1],
            [[[$simpleObject, 1]], clone $simpleObject, null],
            [[[clone $simpleObject, 1]], $simpleObject, null],
            [[[false, 1]], 0, null],
            [[[false, 1]], '', null],
            [[[false, 1]], '0', null],
            [[[false, 1]], false, 1]
        ];
    }

    /**
     * Helper method for building a collection from array
     *
     * @param array $inputPairs
     * @return KeyValueCollection
     */
    private function buildCollection(array $inputPairs): KeyValueCollection {
        $collection = new KeyValueCollection();
        foreach ($inputPairs as $inputPair) {
            $collection->put($inputPair[0], $inputPair[1]);
        }
        return $collection;
    }

    /**
     * Helper method to extract pair array from collection
     *
     * @param KeyValueCollection $collection
     * @return array
     */
    private function extractPairsToArray($collection): array {
        $array = [];
        $pairs = $collection->getAll();
        foreach ($pairs as $pair) {
            $array[] = [$pair->key(), $pair->value()];
        }
        return $array;
    }

    /**
     * Test filling a pair collection
     *
     * @dataProvider PairProvider
     * @param array $inputPairs
     */
    public function testPut(array $inputPairs) {
        $collection = $this->buildCollection($inputPairs);
        $pairs = $this->extractPairsToArray($collection);
        self::assertEquals($inputPairs, $pairs);
    }

    /**
     * Test getting a value from a collection
     *
     * @dataProvider PairProviderGet
     * @param array $inputPairs
     * @param mixed $key
     * @param mixed $expectedValue
     */
    public function testGet(array $inputPairs, $key, $expectedValue) {
        $collection = $this->buildCollection($inputPairs);
        $value = $collection->get($key);
        self::assertEquals($expectedValue, $value);
    }

    /**
     * Test overwriting a value in the collection
     */
    public function testOverwrite() {
        $collection = new KeyValueCollection();
        $collection->put(1, 1);
        $collection->put(1, 2);
        $value = $collection->get(1);
        self::assertEquals(2, $value);
        self::assertEquals(1, $collection->count());
    }

    /**
     * Test contain key method
     *
     * @dataProvider PairProvider
     * @param array $inputPairs
     */
    public function testContainsKey(array $inputPairs) {
        $collection = $this->buildCollection($inputPairs);
        foreach ($inputPairs as $inputPair) {
            self::assertTrue($collection->containsKey($inputPair[0]));
        }
    }

    /**
     * Test removing an item by key
     */
    public function testRemove() {
        $collection = new KeyValueCollection();
        $collection->put(1, 1);
        $collection->removeByKey(1);
        self::assertTrue($collection->isEmpty());
    }

    /**
     * Test iterating trough a collection
     *
     * @dataProvider PairProvider
     * @param array $inputPairs
     */
    public function testIterate(array $inputPairs) {
        $collection = $this->buildCollection($inputPairs);
        $index = 0;
        foreach ($collection as $itemKey => $itemValue) {
            list($expectedKey, $expectedValue) = $inputPairs[$index++];
            self::assertEquals($expectedKey, $itemKey);
            self::assertEquals($expectedValue, $itemValue);
        }
    }

    /**
     * Test put, when key is restricted to specific class
     */
    public function testNamedKeyPut() {
        $collection = new KeyValueCollection(ExampleObject1::class);
        $object = new ExampleObject1();
        $collection->put($object, 1);
        $result = $collection->get($object);
        self::assertEquals(1, $result);
    }

    /**
     * Test put fail, when wrong object is given for key
     * @expectedException \InvalidArgumentException
     */
    public function testNamedKeyPutFailWrongType() {
        $collection = new KeyValueCollection(ExampleObject1::class);
        $object = new ExampleObject2();
        $collection->put($object, 1);
    }

    /**
     * Test put, when value is restricted to specific class
     */
    public function testNamedValuePut() {
        $collection = new KeyValueCollection(null, ExampleObject1::class);
        $object = new ExampleObject1();
        $collection->put(1, $object);
        $result = $collection->get(1);
        self::assertEquals($object, $result);
    }

    /**
     * Test put fail, when wrong object is given for value
     * @expectedException \InvalidArgumentException
     */
    public function testNamedValuePutFailWrongType() {
        $collection = new KeyValueCollection(ExampleObject1::class);
        $object = new ExampleObject2();
        $collection->put(1, $object);
    }

}
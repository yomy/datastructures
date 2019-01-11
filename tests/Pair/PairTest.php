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

namespace YomY\DataStructures\Tests\Pair;

use YomY\DataStructures\Pair\Pair;

/**
 * @package YomY\DataStructures\Tests
 */
class PairTest extends \PHPUnit\Framework\TestCase {

    /**
     * Provides values for pair testing
     *
     * @return array
     */
    public static function ValueProvider(): array {
        return [
            [1, 2],
            ['key', 'value'],
            [null, null],
            [0, 0],
            ['', ''],
            [true, false],
            [false, true],
            [[1], [2]],
            [(object)[1], (object)[2]],
        ];
    }

    /**
     * Tests creating a pair
     *
     * @dataProvider ValueProvider
     * @param mixed $key
     * @param mixed $value
     */
    public function testCreate($key, $value) {
        $pair = new Pair($key, $value);
        self::assertEquals($key, $pair->key());
        self::assertEquals($value, $pair->value());
    }

    /**
     * Tests copying the pair
     *
     * @dataProvider ValueProvider
     * @param mixed $key
     * @param mixed $value
     */
    public function testCopy($key, $value) {
        $pair = new Pair($key, $value);
        $copy = $pair->copy();
        self::assertNotSame($pair, $copy);
        self::assertEquals($key, $copy->key());
        self::assertEquals($value, $copy->value());
    }

}
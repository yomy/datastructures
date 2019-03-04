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

namespace YomY\DataStructures\Tests\Collection\Helper;

use YomY\DataStructures\Collection\ObjectCollection;

/**
 * @package YomY\DataStructures\Tests\Collection\Helper
 * @method ValueObject1[] getAll()
 */
class ValueObject1Collection extends ObjectCollection {

    final public function __construct() {
        parent::__construct(ValueObject1::class);
    }

}
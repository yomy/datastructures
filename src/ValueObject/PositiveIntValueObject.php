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

namespace YomY\DataStructures\ValueObject;

/**
 * Class PositiveIntValueObject
 *
 * @package YomY\ValueObject
 */
class PositiveIntValueObject extends ValueObject {

    /**
     * Validates if a value is valid, throws InvalidArgumentException if not
     *
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    protected static function validateValue($value) {
        parent::validateValue($value);
        if ((int)$value <= 0 || (!\is_int($value) && !\ctype_digit($value))) {
            throw new \InvalidArgumentException('Invalid value');
        }
    }

    /**
     * @param string $class
     * @param mixed $value
     * @return ValueObjectInterface
     */
    protected static function makeInstance(string $class, $value): ValueObjectInterface {
        return parent::makeInstance($class, (int)$value);
    }

}
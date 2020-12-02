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
 * Class EnumValueObject
 *
 * @package YomY\ValueObject
 */
abstract class EnumValueObject extends ValueObject {

    /**
     * @var array
     */
    protected static $availableValues;

    /**
     * @return array
     */
    public static function getAvailableValues(): array {
        $class = static::class;
        if (!isset(static::$availableValues[$class])) {
            try {
                $reflection = new \ReflectionClass($class);
                static::$availableValues[$class] = $reflection->getConstants();
            } catch (\ReflectionException $e) {
                static::$availableValues[$class] = [];
            }
        }
        return static::$availableValues[$class];
    }

    /**
     * @param mixed $value
     * @return string|false
     */
    protected static function getKey($value) {
        return array_search($value, static::getAvailableValues(), true);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isValid($value): bool {
        $key = static::getKey($value);
        return $key !== false;
    }

    /**
     * Validates if a value is valid, throws InvalidArgumentException if not
     *
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    protected static function validateValue($value) {
        parent::validateValue($value);
        $key = static::getKey($value);
        if ($key === false) {
            throw new \InvalidArgumentException('Invalid value');
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return ValueObjectInterface
     * @throws \InvalidArgumentException
     */
    public static function __callStatic(string $name, array $arguments) {
        $availableValues = static::getAvailableValues();
        $value = $availableValues[$name] ?? null;
        return static::instance($value);
    }

}
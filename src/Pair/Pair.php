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

namespace YomY\DataStructures\Pair;

/**
 * Class Pair
 */
class Pair {

    /**
     * @var mixed
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function __construct($key, $value) {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function key() {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function value() {
        return $this->value;
    }

    /**
     * @return Pair
     */
    public function copy(): Pair {
        return new static($this->key(), $this->value());
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return (string)$this->key() . ' => ' . (string)$this->value();
    }

    /**
     * @param string $name
     * @throws \BadFunctionCallException
     */
    public function __get($name) {
        throw new \BadFunctionCallException($name);
    }
    /**
     * @param string $name
     * @param mixed $value
     * @throws \BadFunctionCallException
     */
    public function __set($name, $value) {
        throw new \BadFunctionCallException($name . ' -> ' . $value);
    }
    /**
     * @param string $name
     * @throws \BadFunctionCallException
     */
    public function __isset($name) {
        throw new \BadFunctionCallException($name);
    }

    /**
     * Prevents mutability
     *
     * @throws \BadFunctionCallException
     */
    public function __clone() {
        throw new \BadFunctionCallException('Cloning not allowed');
    }

}
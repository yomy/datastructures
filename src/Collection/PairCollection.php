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

namespace YomY\DataStructures\Collection;

use YomY\DataStructures\Pair\Pair;

/**
 * @package YomY\DataStructures\Collection
 * @method Pair[] getAll()
 * @method Pair getFirst()
 * @method Pair getLast()
 */
class PairCollection extends ObjectCollection {

    /**
     * @var string|null
     */
    private $keyClass;

    /**
     * @var string|null
     */
    private $valueClass;

    /**
     * PairCollection constructor.
     *
     * @param string|null $keyClass
     * @param string|null $valueClass
     */
    public function __construct($keyClass = null, $valueClass = null) {
        if ($keyClass !== null) {
            if (!class_exists($keyClass)) {
                throw new \InvalidArgumentException('Class does not exist');
            }
            $this->keyClass = $keyClass;
        }
        if ($valueClass !== null) {
            if (!class_exists($valueClass)) {
                throw new \InvalidArgumentException('Class does not exist');
            }
            $this->valueClass = $valueClass;
        }
        parent::__construct(Pair::class);
    }

    /**
     * @param Pair $pair
     * @return bool
     */
    private function isValid(Pair $pair): bool {
        $isValid = true;
        if ($this->keyClass !== null && !($pair->key() instanceof $this->keyClass)) {
            $isValid = false;
        }
        if ($this->valueClass !== null && !($pair->value() instanceof $this->valueClass)) {
            $isValid = false;
        }
        return $isValid;
    }

    /**
     * @param mixed $object
     */
    private function validate($object) {
        if (!($object instanceof Pair)) {
            throw new \InvalidArgumentException('Wrong type of Object passed');
        }
        if (!$this->isValid($object)) {
            throw new \InvalidArgumentException('Wrong type of key value pair passed');
        }
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function put($key, $value) {
        $this->add(new Pair($key, $value));
    }

    /**
     * @param mixed $key
     * @return mixed|null
     */
    public function get($key) {
        $result = null;
        $object = $this->getPair($key);
        if ($object) {
            $result = $object->value();
        }
        return $result;
    }

    /**
     * @param mixed $object
     */
    public function add($object) {
        $this->validate($object);
        $existing = $this->getPair($object->key());
        $addNew = false;
        if ($existing === null) {
            $addNew = true;
        } elseif ($existing->value() !== $object->value()) {
            $this->remove($existing);
            $addNew = true;
        }
        if ($addNew) {
            parent::add($object);
        }
    }

    /**
     * @param mixed $key
     * @return Pair|null
     */
    private function getPair($key) {
        $result = null;
        foreach ($this->getAll() as $object) {
            if ($object->key() === $key) {
                $result = $object;
                break;
            }
        }
        return $result;
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function containsKey($key): bool {
        return $this->getPair($key) !== null;
    }

    /**
     * @param mixed $key
     */
    public function removeByKey($key) {
        $object = $this->getPair($key);
        if ($object) {
            parent::remove($object);
        }
    }

    /**
     * @return \Generator|\Traversable
     */
    public function getIterator() {
        foreach ($this->getAll() as $object) {
            yield $object->key() => $object->value();
        }
    }

}
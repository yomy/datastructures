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

namespace YomY\DataStructures\Collection;

/**
 * @package YomY\DataStructures\Collection
 */
class GenericCollection implements \IteratorAggregate, \ArrayAccess, CollectionInterface {

    /**
     * @var array
     */
    private $objects;

    /**
     * Constructor
     */
    public function __construct() {
        $this->clear();
    }

    /**
     * Clears the collection
     */
    public function clear() {
        $this->objects = [];
    }

    /**
     * @param mixed $object
     */
    public function add($object) {
        $this->objects[] = $object;
    }

    /**
     * @param mixed $objectToRemove
     * @return void
     */
    public function remove($objectToRemove) {
        foreach ($this->objects as $key => $object) {
            if ($object === $objectToRemove) {
                unset($this->objects[$key]);
            }
        }
    }

    /**
     * @param array $objects
     */
    public function addArray(array $objects) {
        foreach ($objects as $object) {
            $this->add($object);
        }
    }

    /**
     * @return array
     */
    public function getAll(): array {
        return $this->objects;
    }

    /**
     * @return \Generator|\Traversable
     */
    public function getIterator() {
        foreach ($this->objects as $objectKey => $object) {
            yield $objectKey => $object;
        }
    }

    /**
     * @return int
     */
    public function count(): int {
        return \count($this->objects);
    }

    /**
     * @return string
     */
    public function __toString() {
        return 'Collection(' . \get_class($this) . ')';
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool {
        return \count($this->objects) === 0;
    }

    /**
     * @param mixed $object
     * @return bool
     */
    public function contains($object): bool {
        return \in_array($object, $this->objects, true);
    }

    /**
     * @return CollectionInterface
     */
    public function copy(): CollectionInterface {
        return clone $this;
    }

    /**
     * @param callable $sortMethod
     */
    public function sort(callable $sortMethod) {
        usort($this->objects, function($object1, $object2) use ($sortMethod) {
            return $sortMethod($object1, $object2);
        });
    }

    /**
     * @return CollectionInterface
     */
    public function copyEmpty(): CollectionInterface {
        return new static();
    }

    /**
     * @param callable $filterMethod
     * @return CollectionInterface
     */
    public function filter(callable $filterMethod): CollectionInterface {
        $filtered = $this->copyEmpty();
        foreach ($this->objects as $object) {
            if ($filterMethod($object)) {
                $filtered->add($object);
            }
        }
        return $filtered;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        if ($offset === null) {
            $this->add($value);
        } else {
            throw new \BadMethodCallException('Collection is not indexed');
        }
    }

    /**
     * @param mixed $offset
     * @return bool|void
     */
    public function offsetExists($offset) {
        throw new \BadMethodCallException('Collection is not indexed');
    }

    /**
     * @param mixed $offset
     * @return mixed|void
     */
    public function offsetGet($offset) {
        throw new \BadMethodCallException('Collection is not indexed');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
        throw new \BadMethodCallException('Collection is not indexed');
    }

}
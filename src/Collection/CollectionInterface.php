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
interface CollectionInterface extends \Traversable, \Countable {

    /**
     * @param mixed $object
     * @return void
     */
    public function add($object);

    /**
     * @param array $objects
     * @return void
     */
    public function addArray(array $objects);

    /**
     * @param mixed $object
     * @return mixed
     */
    public function remove($object);

    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @return void
     */
    public function clear();

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @param mixed $object
     * @return bool
     */
    public function contains($object): bool;

    /**
     * Returns a copy of the collection
     *
     * @return CollectionInterface
     */
    public function copy(): CollectionInterface;

    /**
     * Returns an empty object of the same collection type
     *
     * @return CollectionInterface
     */
    public function copyEmpty(): CollectionInterface;

    /**
     * @param CollectionInterface $collection
     * @return void
     */
    public function append(CollectionInterface $collection);

    /**
     * @param callable $sortMethod
     * @return void
     */
    public function sort(callable $sortMethod);

    /**
     * @param callable $filterMethod
     * @return CollectionInterface
     */
    public function filter(callable $filterMethod): CollectionInterface;

    /**
     * @param callable $transformMethod
     * @return array
     */
    public function transformToArray(callable $transformMethod): array;

    /**
     * @param CollectionInterface $collection
     * @param callable|null $transformMethod
     * @return void
     */
    public function transformToCollection(CollectionInterface $collection, callable $transformMethod = null);

    /**
     * @param string $collectionClass
     * @param callable|null $transformMethod
     * @return CollectionInterface
     */
    public function transformToCollectionClass(string $collectionClass, callable $transformMethod = null): CollectionInterface;

}
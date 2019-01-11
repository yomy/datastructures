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
class ObjectCollection extends GenericCollection {

    /**
     * @var string
     */
    private $sourceType;

    /**
     * @param string $sourceType
     */
    public function __construct($sourceType) {
        if (\class_exists($sourceType) || \interface_exists($sourceType)) {
            $this->sourceType = $sourceType;
            parent::__construct();
        } else {
            throw new \InvalidArgumentException('Source class or interface does not exist');
        }
    }

    /**
     * @param mixed $object
     * @return bool
     */
    private function isValid($object): bool {
        return $object instanceof $this->sourceType;
    }

    /**
     * @param mixed $object
     */
    private function validate($object) {
        if (!$this->isValid($object)) {
            throw new \InvalidArgumentException('Wrong type of Object passed');
        }
    }

    /**
     * @param mixed $object
     */
    public function add($object) {
        $this->validate($object);
        return parent::add($object);
    }

    /**
     * Tries to add objects, and returns objects that failed to be added
     *
     * @param array $objects
     * @return mixed
     */
    public function tryAddArray(array $objects) {
        $failedToAdd = [];
        foreach ($objects as $object) {
            try {
                $this->add($object);
            } catch (\InvalidArgumentException $e) {
                $failedToAdd[] = $object;
            }
        }
        return $failedToAdd;
    }

    /**
     * @return CollectionInterface
     */
    public function copyEmpty(): CollectionInterface {
        return new static($this->sourceType);
    }

}
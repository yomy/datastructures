# datastructures
Common data structures for PHP

[![Build Status](https://travis-ci.org/yomy/datastructures.svg?branch=master)](https://travis-ci.org/yomy/datastructures)

## Installation and documentation

- Available as [Composer] package [yomy/datastructures].

## What is this library about

This library adds data structure classes that can be used for better organisation of your objects

## Examples of GenericCollection usage
Creating a collection object:
```php
use YomY\DataStructures\Collection\GenericCollection;
$collection = new GenericCollection();
```

Adding objects to the collection
```php
$collection->add(1);
$collection->add(2);
$collection->add(3);
$collection->add('string');
$collection->add(true);
$collection->add(false);
$collection->add(null);
$collection->add($anObject);
$collection->add(['an', 'array']);
```

Objects can be added multiple times
```php
$collection->add(1);
$collection->add(1);
$collection->add(1);
```

Collection supports adding an array of values at once
```php
$values = [1, 2, 3];
$collection->addArray($values);
```

Removing objects from a collection - removes all occurrences of the object
```php
$collection->remove(1);
```

Getting objects from the collection
```php
$objects = $collection->getAll();
```

You can also iterate trough the collection itself
```php
foreach ($collection as $object) {
    ...
}
```

Count the number of objects in a collection
```php
$count = $collection->count();
```

Verify that the collection is empty
```php
$empty = $collection->isEmpty();
```

Verify that the collection contains an object
```php
$contains = $collection->contains(1);
```

You can shallow copy the collection 
```php
$copy = $collection->copy();
```

You can append another collection to the collection
```php
$collection->append($collection2);
//$collection now has gets items from $collection2 along it's own items
```

Collection supports custom sort of its objects.
```php
$values = [9, 8, 7, 6, 5, 4, 3, 2, 1];
$collection = new GenericCollection();
$collection->addArray($values);
$collection->sort(function($object1, $object2) {
    return $object1 > $object2;
});
//Collection will now have an array like
//[1, 2, 3, 4, 5, 6, 7, 8, 9];

```

Collection can be filtered to get a new collection with filtered objects
```php
$values = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$collection = new GenericCollection();
$collection->addArray($values);
$filtered = $collection->filter(function($object) {
    return $object > 5;
});
//$filtered contains [6, 7, 8, 9]
//original collection is not affected
```

Collection can be transformed to array with a callback method.
This allows extracting underlying data from objects or doing custom calculations
```php
$values = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$collection = new GenericCollection();
$collection->addArray($values);
$transformed = $collection->transformToArray(function($object) {
    //Here we decide what to return in place of each item
    return new CustomWrapper($object);
});
```

Collection can be transformed (appended) to another collection
```php
$values = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$collection = new GenericCollection();
$collection->addArray($values);
$destinationCollection = new GenericCollection();
$destinationCollection->addArray($values);
$collection->transformToCollection($destinationCollection);
//$destinationCollection now has appended items from $collection
```

## Examples of ObjectCollection usage

ObjectCollection is an extension of GenericCollection that is intended
to contain only objects of specific class.

Creating an ObjectCollection of specific type
```php
$objectCollection = new ObjectCollection(ExampleObject1::class);
```

All of the methods from GenericCollection work almost the same, 
except for the fact that only objects of the exact type that was
passed in the Collection constructor are allowed.
```php
$objectCollection = new ObjectCollection(ExampleObject1::class);
$objectCollection->add(new ExampleObject1());
```

Trying to add an object of wrong type will throw an InvalidArgumentException
```php
$objectCollection = new ObjectCollection(ExampleObject1::class);
$objectCollection->add(new DifferentObject());
//This will throw an InvalidArgumentException
``` 

Collection will work with extended objects as well
```php
class ExtendedExampleObject1 extends ExampleObject1 {}
$objectCollection = new ObjectCollection(ExampleObject1::class);
$objectCollection->add(new ExampleObject1());
$objectCollection->add(new ExtendedExampleObject1());
```

The primary intent of the ObjectCollection is to have a custom named collection
```php
class User {...}
class UserCollection extends ObjectCollection {
    public function __construct() {
        parent::__construct(User::class);
    }
}
```

Having a collection such as this allows for type hinting in the code
and we're sure that a collection only consists of objects of specific type
```php
function someMethod(UserCollection $userCollection) {
    foreach ($userCollection as $user) {
        //Here we can use individual users from collection
    }
}

...

$userCollection = new UserCollection();
$userCollection->add($user1);
$userCollection->add($user2);
$userCollection->add($user3);

someMethod($userCollection);
```

In some cases, we need to "try" and fill a collection from an unknown array of objects.
tryAddArray() method will allow for safely trying to add all objects of correct type
and provide an array of objects that have failed to be added.
```php
$failed = $objectCollection->tryAddArray($objects);
//the $failed array will contain objects that were not added to collection
```

## Examples of KeyValueCollection usage

KeyValueCollection is a collection that is intended to contain Key=>Value pairs. 
Internally, this collection holds objects of Pair type, but the intention is not to
use these Pair objects directly, but trough setting a key and value directly on a collection object.
 ```php
 $keyValueCollection = new KeyValueCollection();
 ```
A KeyValueCollection should be used via put() and get() methods.
```php
$keyValueCollection->put('key', 'value');
$value = $keyValueCollection->get('key');
//$value will contain the string 'value'
```

KeyValueCollection has unique keys. This means that putting something on a key that already exists will
owerwrite the value in the collection
```php
$keyValueCollection->put('key', 'value');
$keyValueCollection->put('key', 'newValue');
$value = $keyValueCollection->get('key');
//$value will contain the string 'newValue'
```

Now, you might think that this is simply emulating an associative array, so why would you use this instead?

The power of KeyValueCollection is that it can hold objects as keys, so you can have:
```php
class UserId {...} //A Value Object class
class User {...} //A user details object class

class UserCollection extends KeyValueCollection {
    public function __construct() {
        parent::__construct(UserId::class, User::class);
    }
}

...

//Just for example. These would probably have been built and used inside your application
$userId = new UserId();
$user = new User();

...

$userCollection = new UserCollection();
$userCollection->put($userId, $user);
$user = $userCollection->get($userId);
```
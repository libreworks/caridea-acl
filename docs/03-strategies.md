# Strategies and Loaders

Classes which implement `Caridea\Acl\Strategy` are responsible for returning sets of rules. You must create your own ACL loading strategies. We include absolutely no logic to store and retrieve ACLs.

Why? Well, in our experience, the larger an application gets, the less efficient it is to serialize and store ACLs for any record that might have permissions. We've found that most of the time an application's business rules are determined by record attributes, such as who has created what record, who is the manager of a department, and so on.

By writing your own `Strategy` classes, you control in very fine detail how your permission model is provided.

## ACL Service

The `Caridea\Acl\Service` is the main point of interaction with this library. It takes a single `Strategy` as its argument and uses it to retrieve ACLs. Here are the service methods of note:

* `assert` – Throws a `Caridea\Acl\Exception\Forbidden` if none of the provided `Subject`s have permission for the provided verb in relation to the provided `Target`
* `can` – Returns `true` if one of the provided `Subject`s has permission for the provided verb in relation to the provided `Target`, `false` otherwise

## Strategy Interface

The `Caridea\Acl\Strategy` interface is implemented by classes which find and retrieve an ACL. It has one method of note:

* `load` – Given a `Target`, an array of `Subject`s, and a `Service`, return the corresponding `Acl`

## Loader Interface

The `Caridea\Acl\Loader` interface is an extension of `Strategy`, which introduces a single method:

* `supports` – Given a `Target`, return `true` if the `Loader` is able to return `Acl`s for it

You could create `Loader`s that only support a single type, or a `Loader` which supports several related types. The choice is entirely yours.

## MultiStrategy Interface

The `Caridea\Acl\MultiStrategy` interface is another extension of `Strategy`, which introduces a single method:

* `loadAll` – Given an array of `Target`s, an array of `Subject`s, and a `Service`, return an array of `Acl`s, the keys of which are the string representation of the `Target`

### AbstractMultiLoader

There is an abstract class, `Caridea\Acl\AbstractMultiLoader` which implements both `Loader` and `MultiStrategy`, to be used as a base for your own custom ACL loaders. We extend from it in the below example.

## Implementations

There are two implementations of the `Strategy` interface included with this library.

### Cache Strategy

The `Caridea\Acl\CacheStrategy` class is intended to cache the `Acl`s returned by `Strategy` instances.

```php
// Let's say that $someOtherStrategy is any implementation of \Caridea\Acl\Strategy
$cacheStrategy = new \Caridea\Acl\CacheStrategy($someOtherStrategy);
```

Internally, the ACLs are stored in an `array`, and so the caching only persists for the lifetime of a single request.

### Delegate Strategy

The `Caridea\Acl\DelegateStrategy` class accepts an `array` of `Caridea\Acl\Loader`s and will search through each to attempt to load an `Acl`. If none of the `Loader`s support the provided `Target`, it returns a `DenyAcl`. This class implements `MultiStrategy` and works with `Loaders` that implement `MultiStrategy` as well as ones that don't.

```php
$loaders = [
    $oneLoaderThatImplementsMultiStrategy,
    $anotherLoaderThatDoesNot,
];
$delegateStrategy = new \Caridea\Acl\DelegateStrategy($loaders);
```

The `CacheStrategy` class makes a great wrapper around a `DelegateStrategy` that holds all of your `Loader`s.

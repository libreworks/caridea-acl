# caridea-acl
Caridea is a miniscule PHP application library. This shrimpy fellow is what you'd use when you just want some helping hands and not a full-blown framework.

![](http://libreworks.com/caridea-100.png)

This is its access control component. You can create lists of permissions from any source you wish.

[![Packagist](https://img.shields.io/packagist/v/caridea/acl.svg)](https://packagist.org/packages/caridea/acl)
[![Build Status](https://travis-ci.org/libreworks/caridea-acl.svg)](https://travis-ci.org/libreworks/caridea-acl)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/libreworks/caridea-acl/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/libreworks/caridea-acl/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/libreworks/caridea-acl/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/libreworks/caridea-acl/?branch=master)

## Installation

You can install this library using Composer:

```console
$ composer require caridea/acl
```

* The master branch (version 3.x) of this project requires PHP 7.1 and has no dependencies.
* Version 2.x of this project requires PHP 7.0 and has no dependencies.
* Version 1.x of this project requires PHP 5.5 and has no dependencies.

## Compliance

Releases of this library will conform to [Semantic Versioning](http://semver.org).

Our code is intended to comply with [PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/), and [PSR-4](http://www.php-fig.org/psr/psr-4/). If you find any issues related to standards compliance, please send a pull request!

## Documentation

* Head over to [Read the Docs](http://caridea-acl.readthedocs.io/en/latest/)

## Definitions

Our permission API deals with three concepts: Subjects, Verbs, and Targets.

A *target* is something that can be protected. It has a type and an identifier. It could be a record from your database, a controller method in your application, or a URL.

A *subject* is a user or role that can be allowed or denied access. `caridea-acl` ships with two kinds of `Subject`s: *principal* and *role*. For instance, the currently authenticated user has a principal `Subject` with the username, and several role `Subject`s with the role names (e.g. `admin`, `user`, `us-citizen`).

A *verb* is the action the `Subject` can take on the `Target` (e.g. *read*, *create*, *submit*).

`Target` and `Subject` are classes, not interfaces. Since we intend ACLs to be immutable and potentially serializable, we'd rather you not add interfaces onto your own domain classes.

## Examples

You must create your own ACL loaders. We include absolutely no logic to store and retrieve ACLs.

Why? Well, in our experience, the larger an application gets, the less efficient it is to serialize and store ACLs for any record that might have permissions. We've found that most of the time an application's business rules are determined by record attributes, such as who's created what record, who's the manager of a department, and so on.

By writing your own `Loader`s, you control in very fine detail how your permission model is provided.

```php
class MyLoader implements \Caridea\Acl\Loader
{
    public function supports(Target $target)
    {
        return $target->getType() == 'foobar';
    }

    public function load(Target $target, array $subjects, Service $service)
    {
        // some custom method to load my database record
        try {
            $record = MyRecord::loadFromDatabase($target->getId());
        } catch (\Exception $e) {
            throw new \Caridea\Acl\Exception\Unloadable("Could not load record", 0, $e);
        }
        // load the parent record's ACL
        $parent = $service->get(new Target('foobar', $record['parent']), $subjects);
        // create the rules and return the final constructed ACL
        $rules = [];
        foreach ($subjects as $subject) {
            if ($subject->getType() == 'role' &&
                $subject->getId() == 'admin') {
                // allow "admin" role for all permissions
                $rules[] = Rule::allow($subject);
            } elseif ($subject->getType() == 'role' &&
                $subject->getId() == 'user') {
                // allow "user" role the read permission
                $rules[] = Rule::allow($subject, ['read']);
            } elseif ($subject->getType() == 'principal' &&
                $subject->getId() == $record['owner']) {
                // allow the record owner CRUD permissions
                $rules[] = Rule::allow($subject, ['create', 'read', 'update', 'delete']);
            }
        }
        return new RuleAcl($target, $subjects, $rules, $parent);
    }
}
```

Then put it all together.

```php
// A list of all of your custom loaders
$loaders = [new MyLoader()];
// Use the Cache Strategy to cache lookups
$strategy = new \Caridea\Acl\CacheStrategy(
    new \Caridea\Acl\DelegateStrategy($loaders);
);
$service = new \Caridea\Acl\Service($strategy);

$subjects = MyClass::getSubjects(); // determine which subjects the user has
$target = new Target('foobar', 123);

$allowed = $service->can($subjects, 'delete', $target);

try {
    $service->assert($subjects, 'delete', $target);
} catch (\Caridea\Acl\Exception\Forbidden $e) {
    // not allowed!
}
```

You might consider wiring up all your loaders and the `Service` class using dependency injection, for instance with `caridea/container`.

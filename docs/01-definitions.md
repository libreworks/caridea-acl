# Definitions

Our permission API deals with three concepts: Subjects, Verbs, and Targets.

A *target* is something that can be protected. It has a type and an identifier. It could be a record from your database, a controller method in your application, or a URL.

A *subject* is a user or role that can be allowed or denied access. `caridea-acl` ships with two kinds of `Subject`s: *principal* and *role*. For instance, the currently authenticated user has a principal `Subject` with the username, and several role `Subject`s with the role names (e.g. `admin`, `user`, `us-citizen`).

A *verb* is the action the `Subject` can take on the `Target` (e.g. *read*, *create*, *submit*).

`Target` and `Subject` are classes, not interfaces. Since we intend ACLs to be immutable and potentially serializable, we'd rather you not add interfaces onto your own domain classes.

## Target

The `Caridea\Acl\Target` class is an immutable class which holds the details of an ACL *target*. It has two methods of note.

* `getId` – Returns the target identifier (which could be of any serializable type)
* `getType` – Returns the `string` type of a target, usually a PHP class name

You can create targets using the class constructor.

```php
$target = new \Caridea\Acl\Target('foo', 'bar');
```

## Subject

The `Caridea\Acl\Subject` class is an immutable class which holds the details of an ACL *subject*. It has two methods of note.

* `getId` – Returns the `string` subject identifier
* `getType` – Returns the `string` type of a target

There are only two types of subjects at this time: principals and roles. You can create targets using static class methods.

```php
$user = \Caridea\Acl\Subject::principal('someone@example.com');
$role = \Caridea\Acl\Subject::role('admin');
```

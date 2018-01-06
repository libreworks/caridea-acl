# Access Control Lists

The `Caridea\Acl\Acl` interface is the contract for Access Control Lists in this library. Implementations of this class are meant to be serializable. It's recommended that they're also immutable, but this is not a requirement. ACLs can have parent ACLs, which are consulted in the event that the child contains no applicable rules for a test.

## Working with the Interface

There are three methods of note when working with an `Acl`:

* `can` – Provided an `array` of `Subject`s and a verb, this method returns `true` if any of the `Subject`s can perform the provided verb
  * The parent ACL will be consulted if this one has no corresponding rules.
* `getParent` – Returns the parent `Acl` or `null` if one was not specified
* `getTarget` – Returns the `Target` for which this `Acl` applies

There are two bundled implementations of the `Caridea\Acl\Acl` interface: `Caridea\Acl\RuleAcl` and `Caridea\Acl\DenyAcl`.

## DenyAcl

The `Caridea\Acl\DenyAcl` class is a no-op implementation which always denies access. It's useful for unit tests and as a null object.

## RuleAcl

The `Caridea\Acl\RuleAcl` class is an immutable and serializable list of access rules.

### Rules

The `RuleAcl` internally stores `Caridea\Acl\Rule` objects. They are immutable, serializable classes which either grant or deny a subject access to a verb.

```php
$admin = \Caridea\Acl\Subject::role('admin');
$blocked = \Caridea\Acl\Subject::role('blocked');
$allow = \Caridea\Acl\Rule::allow($admin, ['read', 'write', 'delete']);
$deny = \Caridea\Acl\Rule::deny($blocked, ['read', 'write', 'delete']);
```

### Creating a Rule ACL

To create a `RuleAcl`, simply provide everything necessary to the constructor.

```php
$subjects = [$admin, $blocked];
$rules = [$allow, $deny];
$target = new \Caridea\Acl\Target('foo', 'bar');
$parent = new \Caridea\Acl\DenyAcl();
$acl = new \Caridea\Acl\RuleAcl(
    $target,
    $subjects,
    $rules,
    $parent
);
$acl->can([$admin], 'read');
// true

$acl->can([$blocked], 'read');
// false

$acl->can([$blocked], 'dance');
// false; delegates to parent, since no rules exist for the verb "dance"

$acl->can([\Caridea\Acl\Subject::principal('nobody@example.com')], 'read');
// InvalidArgumentException("This ACL does not apply to one of the provided subjects")
```

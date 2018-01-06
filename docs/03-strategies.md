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

* `load` – Given a `Target`, an array of `Subject`s, and a `Caridea\Acl\Service`

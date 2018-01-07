# Examples

Let's put everything from the last three chapters together, shall we?

Here is an example implementation of a `Loader` and a `MultiStrategy`.

```php
namespace My;

use Caridea\Acl\Target;
use Caridea\Acl\RuleAcl;
use Caridea\Acl\Rule;
use Caridea\Acl\Service;

/**
 * Loads ACLs for \My\Foobar entities.
 */
class FoobarLoader extends \Caridea\Acl\AbstractMultiLoader
{
    /**
     * @var \My\FoobarDao
     */
    private $dao;

    /**
     * Creates a new \My\FoobarLoader
     *
     * @param \My\FoobarDao $dao  The DAO
     */
    public function __construct(FoobarDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Target $target)
    {
        return $target->getType() == 'foobar';
    }

    /**
     * {@inheritDoc}
     */
    public function loadAll(array $targets, array $subjects, Service $service): array
    {
        $acls = [];
        if (!empty($targets)) {
            // Gather up all our record IDs.
            // Let's make sure all are supported Targets.
            $ids = [];
            foreach ($targets as $target) {
                if (!$this->supports($target)) {
                    throw new \InvalidArgumentException("Unsupported type: " . $target->getType());
                }
                $ids[] = $target->getId();
            }

            // Load all entities.
            // Let's say this method returns an array keyed by ID.
            $entities = $this->dao->someMethodToLoadSeveralEntitiesByIds($ids);

            // Fill in all ACLs
            foreach ($targets as $target) {
                $entity = $entities[$target->getId()] ?? null;
                if ($entity === null) {
                    throw new \Caridea\Acl\Exception\Unloadable("Could not load record: " . $target->getId());
                }
                $acls[(string) $target] = $this->loadAcl($entity, $target, $subjects, $service);
            }
        }
        return $acls;
    }

    /**
     * Loads the actual ACL.
     *
     * @param \My\Foobar $entity
     * @param \Caridea\Acl\Target $target
     * @param \Caridea\Acl\Subject[] $subjects
     * @param \Caridea\Acl\Service $service
     * @return \Caridea\Acl\RuleAcl
     */
    protected function loadAcl(Foobar $entity, Target $target, array $subjects, Service $service)
    {
        // load the parent record's ACL
        $parent = $entity->getParent() === null ?
            $service->get(new Target('foobar', $entity->getParent()), $subjects)
            : null;

        // create the rules
        $rules = [];
        foreach ($subjects as $subject) {
            if ($subject->getType() == 'role' && $subject->getId() == 'admin') {
                // allow "admin" role for all permissions
                $rules[] = Rule::allow($subject);
            } elseif ($subject->getType() == 'role' && $subject->getId() == 'user') {
                // allow "user" role the read permission
                $rules[] = Rule::allow($subject, ['read']);
            } elseif ($subject->getType() == 'principal' && $subject->getId() == $record->getOwner()) {
                // allow the record owner CRUD permissions
                $rules[] = Rule::allow($subject, ['create', 'read', 'update', 'delete']);
            }
        }

        // return the final constructed ACL
        return new RuleAcl($target, $subjects, $rules, $parent);
    }
}
```

Now, let's see how it all works together.

```php
// A list of all of your custom loaders
$loaders = [
    new \My\FoobarLoader($foobarDao),
];

// Use the Cache Strategy to cache lookups
$strategy = new \Caridea\Acl\CacheStrategy(
    new \Caridea\Acl\DelegateStrategy($loaders);
);
$service = new \Caridea\Acl\Service($strategy);

// determine which subjects the user has
$subjects = MyClass::getSubjects();
$target = new Target('foobar', 123);

$allowed = $service->can($subjects, 'delete', $target);

try {
    $service->assert($subjects, 'delete', $target);
} catch (\Caridea\Acl\Exception\Forbidden $e) {
    // not allowed!
}
```

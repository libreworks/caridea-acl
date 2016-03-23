<?hh // strict

namespace Caridea\Acl;

class RuleAcl implements Acl
{
    private Target $resource;

    public function __construct(Target $target, array<Subject> $subjects, array<Rule> $rules, ?Acl $parent = null)
    {
        $this->resource = $target;
    }
    
    protected function hasAllSubjects(array<Subject> $subjects): bool
    {
        return false;
    }
    
    public function can(array<Subject> $subjects, string $verb): bool
    {
        return false;
    }

    public function getParent(): ?Acl
    {
        return null;
    }
    
    public function getTarget(): Target
    {
        return $this->resource;
    }
}

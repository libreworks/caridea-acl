<?hh // strict

namespace Caridea\Acl;

class DenyAcl implements Acl
{
    private Target $target;

    public function __construct(Target $target)
    {
        $this->target = $target;
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
        return $this->target;
    }
}

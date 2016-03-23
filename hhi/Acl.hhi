<?hh // strict

namespace Caridea\Acl;

interface Acl
{
    public function can(array<Subject> $subjects, string $verb): bool;

    public function getParent(): ?Acl;
    
    public function getTarget(): Target;
}

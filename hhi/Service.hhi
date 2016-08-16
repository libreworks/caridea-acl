<?hh // strict

namespace Caridea\Acl;

class Service
{
    private Strategy $strategy;

    public function __construct(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function assert(array<Subject> $subjects, string $verb, Target $target): void
    {
    }

    public function can(array<Subject> $subjects, string $verb, Target $target): bool
    {
        return false;
    }

    public function get(Target $target, array<Subject> $subjects): Acl
    {
        return $this->strategy->load($target, $subjects, $this);
    }

    public function getAll(array<Target> $targets, array<Subject> $subjects): array<string,Acl>
    {
        return [];
    }
}

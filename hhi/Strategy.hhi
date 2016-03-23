<?hh // strict

namespace Caridea\Acl;

interface Strategy
{
    public function load(Target $target, array<Subject> $subjects, Service $service): Acl;
}

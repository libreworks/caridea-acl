<?hh // strict

namespace Caridea\Acl;

interface MultiStrategy extends Strategy
{
    public function loadAll(array<Target> $targets, array<Subject> $subjects, Service $service): array<string,Acl>;
}

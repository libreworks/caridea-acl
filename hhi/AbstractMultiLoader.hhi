<?hh // strict

namespace Caridea\Acl;

abstract class AbstractMultiLoader implements Loader, MultiStrategy
{
    public function load(Target $target, array<Subject> $subjects, Service $service): Acl
    {
        $acls = $this->loadAll([$target], $subjects, $service);
        return $acls[(string) $target];
    }
}

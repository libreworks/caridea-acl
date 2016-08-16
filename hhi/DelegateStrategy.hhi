<?hh // strict

namespace Caridea\Acl;

class DelegateStrategy implements MultiStrategy
{
    private \SplFixedArray<int,Loader> $loaders;

    public function __construct(array<Loader> $loaders)
    {
        $this->loaders = \SplFixedArray::fromArray($loaders);
    }

    public function load(Target $target, array<Subject> $subjects, Service $service): Acl
    {
        return $this->loaders->offsetGet(0)->load($target, $subjects, $service);
    }

    public function loadAll(array<Target> $targets, array<Subject> $subjects, Service $service): array<string,Acl>
    {
        return [];
    }
}

<?hh // strict

namespace Caridea\Acl;

class CacheStrategy implements MultiStrategy
{
    protected array<Acl> $cache = [];

    protected Strategy $delegate;

    public function __construct(Strategy $delegate)
    {
        $this->delegate = $delegate;
    }

    public function load(Target $target, array<Subject> $subjects, Service $service): Acl
    {
        return $this->delegate->load($target, $subjects, $service);
    }

    public function loadAll(array<Target> $target, array<Subject> $subjects, Service $service): array<string,Acl>
    {
        return [];
    }

    protected function buildKey(Target $targets, array<Subject> $subjects): string
    {
        return '';
    }

}

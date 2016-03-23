<?hh // strict

namespace Caridea\Acl;

interface Loader extends Strategy
{
    public function supports(Target $target): bool;
}

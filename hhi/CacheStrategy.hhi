<?hh // strict

namespace Caridea\Acl;

class CacheStrategy implements Strategy
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

    protected function buildKey(Target $target, array<Subject> $subjects): string
    {
        $key = (string) $target;
        foreach ($subjects as $subject) {
            if (!($subject instanceof Subject)) {
                throw new \InvalidArgumentException("Only instances of Subject are permitted in the subjects argument");
            }
            $key .= ";{$subject}";
        }
        return $key;
    }
}

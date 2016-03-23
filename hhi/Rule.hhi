<?hh // strict

namespace Caridea\Acl;

class Rule
{
    protected function __construct(Subject $subject, bool $allowed, ?array<string> $verbs = null)
    {
    }

    public function isAllowed(): bool
    {
        return false;
    }

    public function match(Subject $subject, string $verb): bool
    {
        return false;
    }

    public static function allow(Subject $subject, ?array<string> $verbs = null): Rule
    {
        return new self($subject, true, $verbs);
    }
    
    public static function deny(Subject $subject, ?array<string> $verbs = null): Rule
    {
        return new self($subject, false, $verbs);
    }
}

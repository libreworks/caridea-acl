<?hh // strict

namespace Caridea\Acl;

class Subject
{
    const PRINCIPAL = 'principal';
    const ROLE = 'role';
    
    protected function __construct(string $type, string $id)
    {
    }

    public function getId(): string
    {
        return '';
    }

    public function getType(): string
    {
        return '';
    }
    
    public function __toString(): string
    {
        return '';
    }

    public static function principal(string $id): Subject
    {
        return new self(self::PRINCIPAL, $id);
    }
    
    public static function role(string $id): Subject
    {
        return new self(self::ROLE, $id);
    }
}

<?hh // strict

namespace Caridea\Acl;

class Target
{
    public function __construct(string $type, mixed $id)
    {
    }
    
    public function getId(): mixed
    {
        return null;
    }

    public function getType(): string
    {
        return '';
    }

    public function __toString(): string
    {
        return '';
    }
}

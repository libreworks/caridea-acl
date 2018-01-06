<?php
declare(strict_types=1);
/**
 * Caridea
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
namespace Caridea\Acl;

/**
 * An authorization subject: usually either a role or a principal.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
class Subject
{
    const PRINCIPAL = 'principal';
    const ROLE = 'role';

    /**
     * @var string The subject type
     */
    private $type;
    /**
     * @var string The subject identifier
     */
    private $id;

    /**
     * Creates a new BasicSubject.
     *
     * @param string $type The subject type (e.g. "role", "principal")
     * @param string $id The subject identifier
     */
    protected function __construct(string $type, string $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * Gets the identifier of the subject, usually a unique key.
     *
     * @return string The subject identifier
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Gets the type of the subject.
     *
     * @return string The subject type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Gets a string representation of this subject.
     *
     * @return string The string representation
     */
    public function __toString(): string
    {
        return "{$this->type}:{$this->id}";
    }

    /**
     * Gets a Subject for a principal (such as a username).
     *
     * @param string $id The subject identifier
     * @return Subject The subject created!
     */
    public static function principal(string $id): Subject
    {
        return new self(self::PRINCIPAL, $id);
    }

    /**
     * Gets a Subject for a role.
     *
     * @param string $id The subject identifier
     * @return Subject The subject created
     */
    public static function role(string $id): Subject
    {
        return new self(self::ROLE, $id);
    }
}

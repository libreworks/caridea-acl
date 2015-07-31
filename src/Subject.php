<?php
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
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
namespace Caridea\Acl;

/**
 * An authorization subject: usually either a role or a principal.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
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
    protected function __construct($type, $id)
    {
        $this->type = (string) $type;
        $this->id = (string) $id;
    }

    /**
     * Gets the identifier of the subject, usually a unique key.
     *
     * @return string The subject identifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the type of the subject.
     *
     * @return string The subject type
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Gets a string representation of this subject.
     *
     * @return string The string representation
     */
    public function __toString()
    {
        return "{$this->type}:{$this->id}";
    }
    
    /**
     * Gets a Subject for a principal (such as a username).
     *
     * @param string $id The subject identifier
     * @return Subject The subject created
     */
    public static function principal($id)
    {
        return new self(self::PRINCIPAL, $id);
    }
    
    /**
     * Gets a Subject for a role.
     *
     * @param string $id The subject identifier
     * @return Subject The subject created
     */
    public static function role($id)
    {
        return new self(self::ROLE, $id);
    }
}

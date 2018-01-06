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
 * Immutable reference to a domain object.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
class Target
{
    /**
     * @var string The resource type
     */
    private $type;
    /**
     * @var mixed The resource identifier
     */
    private $id;

    /**
     * Creates a new Target.
     *
     * @param string $type The resource type
     * @param mixed $id The resource identifier
     */
    public function __construct(string $type, $id)
    {
        $this->type = (string) $type;
        if (strlen(trim($this->type)) == 0) {
            throw new \InvalidArgumentException("Target type can't be blank");
        }
        $this->id = $id;
    }
    
    /**
     * Gets the identifier of the resource, usually a unique key.
     *
     * @return mixed The resource identifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the type of resource, usually a domain class name.
     *
     * @return string The resource type
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    /**
     * A string representation of this object.
     *
     * @return string The string representation
     */
    public function __toString(): string
    {
        return "{$this->type}#{$this->id}";
    }
}

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
 * Service for working with permissions.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Service
{
    /**
     * @var Strategy The loading strategy
     */
    private $strategy;
    
    /**
     * Creates a new Service.
     *
     * @param \Caridea\Acl\Strategy $strategy The loading strategy
     */
    public function __construct(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }
    
    /**
     * Asserts that one of the provided subjects can verb the Resource.
     *
     * @param Subject[] $subjects An array of `Subject`s
     * @param string $verb The verb (e.g. `read`, `create`, `delete`)
     * @param \Caridea\Acl\Resource $resource The resource
     * @throws Exception\Forbidden if the subject cannot *verb* the Resource
     */
    public function assert(array $subjects, $verb, Resource $resource)
    {
        try {
            if ($this->get($resource, $subjects)->can($subjects, $verb)) {
                return;
            }
        } catch (\Exception $ex) {
            throw new Exception\Forbidden("Access denied to $verb the resource", 0, $ex);
        }
        throw new Exception\Forbidden("Access denied to $verb the resource");
    }
    
    /**
     * Whether any of the provided subjects has permission to verb the Resource.
     *
     * @param Subject[] $subjects An array of `Subject`s
     * @param string $verb The verb (e.g. `read`, `create`, `delete`)
     * @param \Caridea\Acl\Resource $resource The resource
     * @return bool Whether one of the subjects can *verb* the provided Resource
     */
    public function can(array $subjects, $verb, Resource $resource)
    {
        try {
            return $this->get($resource, $subjects)->can($subjects, $verb);
        } catch (\Exception $ex) {
        }
        return false;
    }
    
    /**
     * Gets an access control list for a Resource.
     *
     * @param Resource $resource The Resource whose ACL will be loaded
     * @param Subject[] $subjects An array of `Subject`s
     * @return Acl The Acl found
     * @throws Exception\Unloadable If the resource provided is invalid
     * @throws \InvalidArgumentException If the `subjects` argument contains invalid values
     */
    public function get(Resource $resource, array $subjects)
    {
        return $this->strategy->load($resource, $subjects, $this);
    }
}

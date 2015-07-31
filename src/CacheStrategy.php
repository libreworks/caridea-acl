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
 * Caches ACLs based on Resource and Subjects.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class CacheStrategy implements Strategy
{
    /**
     * @var Acl[] contains the ACLs indexed by Resource and Subjects string
     */
    protected $cache = [];
    /**
     * @var Strategy The actual loading strategy
     */
    protected $delegate;
    
    /**
     * Creates a new CacheStrategy.
     *
     * @param \Caridea\Acl\Strategy $delegate The actual loading strategy to use
     */
    public function __construct(Strategy $delegate)
    {
        $this->delegate = $delegate;
    }
    
    /**
     * Loads the ACL for a Resource.
     *
     * @param Resource $resource The `Resource` whose ACL will be loaded
     * @param Subject[] $subjects An array of `Subject`s
     * @param Service $service The ACL service (to load parent ACLs)
     * @return Acl The loaded ACL
     * @throws Exception\Unloadable If the resource provided is invalid
     * @throws \InvalidArgumentException If the `subjects` argument contains invalid values
     */
    public function load(Resource $resource, array $subjects, Service $service)
    {
        $key = $this->buildKey($resource, $subjects);
        if (!isset($this->cache[$key])) {
            $acl = $this->delegate->load($resource, $subjects, $service);
            $this->cache[$key] = $acl;
        }
        return $this->cache[$key];
    }
    
    /**
     * Generates the key to use for caching the ACL.
     *
     * @param \Caridea\Acl\Resource $resource The `Resource` whose ACL will be loaded
     * @param array $subjects An array of `Subject`s
     * @return string The cache key
     * @throws \InvalidArgumentException If the `subjects` argument contains invalid values
     */
    protected function buildKey(Resource $resource, array $subjects)
    {
        $key = (string) $resource;
        foreach ($subjects as $subject) {
            if (!($subject instanceof Subject)) {
                throw new \InvalidArgumentException("Only instances of Subject are permitted in the subjects argument");
            }
            $key .= ";{$subject}";
        }
        return $key;
    }
}

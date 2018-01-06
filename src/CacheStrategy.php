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
 * Caches ACLs based on Target and Subjects.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
class CacheStrategy implements MultiStrategy
{
    /**
     * @var array<string,\Caridea\Acl\Acl> contains the ACLs indexed by Target and Subjects string
     */
    protected $cache = [];
    /**
     * @var \Caridea\Acl\Strategy The actual loading strategy
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
     * Loads the ACL for a Target.
     *
     * @param \Caridea\Acl\Target $target The `Target` whose ACL will be loaded
     * @param \Caridea\Acl\Subject[] $subjects An array of `Subject`s
     * @param \Caridea\Acl\Service $service The ACL service (to load parent ACLs)
     * @return \Caridea\Acl\Acl The loaded ACL
     * @throws \Caridea\Acl\Exception\Unloadable If the resource provided is invalid
     * @throws \InvalidArgumentException If the `subjects` argument contains invalid values
     */
    public function load(Target $target, array $subjects, Service $service): Acl
    {
        $key = $this->buildKey($target, $subjects);
        if (!isset($this->cache[$key])) {
            $acl = $this->delegate->load($target, $subjects, $service);
            $this->cache[$key] = $acl;
        }
        return $this->cache[$key];
    }

    /**
     * Loads the ACLs for several Targets.
     *
     * @since 2.1.0
     * @param \Caridea\Acl\Target[] $targets The `Target` whose ACL will be loaded
     * @param \Caridea\Acl\Subject[] $subjects An array of `Subject`s
     * @param \Caridea\Acl\Service $service The ACL service (to load parent ACLs)
     * @return array<string,\Caridea\Acl\Acl> The loaded ACLs
     * @throws \Caridea\Acl\Exception\Unloadable If the target provided is invalid
     * @throws \InvalidArgumentException If the `subjects` argument contains invalid values
     */
    public function loadAll(array $targets, array $subjects, Service $service): array
    {
        $acls = [];
        if ($this->delegate instanceof MultiStrategy) {
            $oids = array_merge($targets);
            foreach ($targets as $i => $target) {
                if (!($target instanceof Target)) {
                    throw new \InvalidArgumentException("Only instances of Target are permitted in the targets argument");
                }
                $key = $this->buildKey($target, $subjects);
                if (isset($this->cache[$key])) {
                    $acls[(string)$target] = $this->cache[$key];
                    unset($oids[$i]);
                }
            }
            if (!empty($oids)) {
                $a = $this->delegate->loadAll($oids, $subjects, $service);
                foreach ($a as $acl) {
                    $key = $this->buildKey($acl->getTarget(), $subjects);
                    $this->cache[$key] = $acl;
                    $acls[(string)$acl->getTarget()] = $acl;
                }
            }
        } else {
            foreach ($targets as $target) {
                if (!($target instanceof Target)) {
                    throw new \InvalidArgumentException("Only instances of Target are permitted in the targets argument");
                }
                $acls[(string) $target] = $this->load($target, $subjects, $service);
            }
        }
        return $acls;
    }

    /**
     * Generates the key to use for caching the ACL.
     *
     * @param \Caridea\Acl\Target $target The `Target` whose ACL will be loaded
     * @param array $subjects An array of `Subject`s
     * @return string The cache key
     * @throws \InvalidArgumentException If the `subjects` argument contains invalid values
     */
    protected function buildKey(Target $target, array $subjects): string
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

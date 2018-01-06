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
 * Service for working with permissions.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
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
     * Asserts that one of the provided subjects can verb the Target.
     *
     * @param Subject[] $subjects An array of `Subject`s
     * @param string $verb The verb (e.g. `read`, `create`, `delete`)
     * @param \Caridea\Acl\Target $target The target
     * @throws Exception\Forbidden if the subject cannot *verb* the Target
     */
    public function assert(array $subjects, string $verb, Target $target): void
    {
        try {
            if ($this->get($target, $subjects)->can($subjects, $verb)) {
                return;
            }
        } catch (\Exception $ex) {
            throw new Exception\Forbidden("Access denied to $verb the target", 0, $ex);
        }
        throw new Exception\Forbidden("Access denied to $verb the target");
    }

    /**
     * Whether any of the provided subjects has permission to verb the Target.
     *
     * @param Subject[] $subjects An array of `Subject`s
     * @param string $verb The verb (e.g. `read`, `create`, `delete`)
     * @param \Caridea\Acl\Target $target The target
     * @return bool Whether one of the subjects can *verb* the provided Target
     */
    public function can(array $subjects, string $verb, Target $target): bool
    {
        try {
            return $this->get($target, $subjects)->can($subjects, $verb);
        } catch (\Exception $ex) {
            // just return false below
        }
        return false;
    }

    /**
     * Gets an access control list for a Target.
     *
     * @param Target $target The Target whose ACL will be loaded
     * @param Subject[] $subjects An array of `Subject`s
     * @return Acl The Acl found
     * @throws Exception\Unloadable If the target provided is invalid
     * @throws \InvalidArgumentException If the `subjects` argument contains invalid values
     */
    public function get(Target $target, array $subjects): Acl
    {
        return $this->strategy->load($target, $subjects, $this);
    }

    /**
     * Gets access control lists for several Targets.
     *
     * @since 2.1.0
     * @param \Caridea\Acl\Target[] $targets The `Target` whose ACL will be loaded
     * @param \Caridea\Acl\Subject[] $subjects An array of `Subject`s
     * @return array<string,\Caridea\Acl\Acl> The loaded ACLs
     * @throws \Caridea\Acl\Exception\Unloadable If the target provided is invalid
     * @throws \InvalidArgumentException If the `targets` or `subjects` argument contains invalid values
     */
    public function getAll(array $targets, array $subjects): array
    {
        $acls = [];
        if ($this->strategy instanceof MultiStrategy) {
            $acls = $this->strategy->loadAll($targets, $subjects, $this);
        } else {
            foreach ($targets as $target) {
                $acls[(string)$target] = $this->strategy->load($target, $subjects, $this);
            }
        }
        $missing = array_diff(array_map(function ($a) {
            return (string) $a;
        }, $targets), array_keys($acls));
        // Check every requested target was found
        if (!empty($missing)) {
            throw new Exception\Unloadable("Unable to load ACL for " . implode(", ", $missing));
        }
        return $acls;
    }
}

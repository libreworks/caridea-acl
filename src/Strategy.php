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
 * Implements the logic of finding and retrieving an ACL.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
interface Strategy
{
    /**
     * Loads the ACL for a Target.
     *
     * Implementations *must* return an ACL, never `null`. If there are no rules
     * available for the subjects provided, return the `DenyAcl`.
     *
     * @param \Caridea\Acl\Target $target The `Target` whose ACL will be loaded
     * @param \Caridea\Acl\Subject[] $subjects An array of `Subject`s
     * @param \Caridea\Acl\Service $service The ACL service (to load parent ACLs)
     * @return \Caridea\Acl\Acl The loaded ACL
     * @throws \Caridea\Acl\Exception\Unloadable If the target provided is invalid
     * @throws \InvalidArgumentException If the `subjects` argument contains invalid values
     */
    public function load(Target $target, array $subjects, Service $service): Acl;
}

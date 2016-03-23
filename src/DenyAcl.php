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
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
namespace Caridea\Acl;

/**
 * An ACL that always denies. It's a no-op.
 *
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class DenyAcl implements Acl
{
    /**
     * @var \Caridea\Acl\Target The target
     */
    private $target;
    
    /**
     * Creates a new DenyAcl.
     *
     * @param \Caridea\Acl\Target $target The target
     */
    public function __construct(Target $target)
    {
        $this->target = $target;
    }
    
    /**
     * Tests whether any of the subjects can perform the provided verb.
     *
     * @param \Caridea\Acl\Subject[] $subjects A non-empty array of subjects.
     * @param string $verb The verb to test.
     * @return bool Always returns false
     */
    public function can(array $subjects, string $verb): bool
    {
        return false;
    }

    /**
     * Gets the parent ACL.
     *
     * @return \Caridea\Acl\Acl The parent ACL or null
     */
    public function getParent()
    {
        return null;
    }
    
    /**
     * Gets the Target for this ACL.
     *
     * @return \Caridea\Acl\Target The target for this ACL
     */
    public function getTarget(): Target
    {
        return $this->target;
    }
}

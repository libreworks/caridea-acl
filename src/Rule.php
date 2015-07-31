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
 * An ACL rule.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Rule
{
    /**
     * @var Subject The subject
     */
    private $subject;
    /**
     * @var array|null An array of verbs granted access, or null for all verbs
     */
    private $verbs;
    /**
     * @var bool Whether the Subject is allowed or forbidden
     */
    private $allowed;
    
    /**
     * Creates a new Rule.
     *
     * @param \Caridea\Acl\Subject $subject The subject
     * @param bool $allowed Whether this is an allowing rule
     * @param array $verbs Array of verbs, or null for all verbs
     */
    protected function __construct(Subject $subject, $allowed, array $verbs = null)
    {
        $this->subject = $subject;
        $this->allowed = (bool) $allowed;
        $this->verbs = empty($verbs) ? null : $verbs;
    }
    
    /**
     * Gets whether this is an allowing rule.
     *
     * @return bool Whether this is an allowing rule
     */
    public function isAllowed()
    {
        return $this->allowed;
    }
    
    /**
     * Gets whether this Rule matches the `Subject` and verb provided.
     *
     * @param \Caridea\Acl\Subject $subject The subject to check
     * @param string $verb The verb to check
     * @return bool Whether this Rule matches the arguments provided
     */
    public function match(Subject $subject, $verb)
    {
        return $this->subject->getType() == $subject->getType() &&
            $this->subject->getId() === $subject->getId() &&
            ($this->verbs === null || in_array($verb, $this->verbs, true));
    }
    
    /**
     * Creates an allowing Rule.
     *
     * @param \Caridea\Acl\Subject $subject The subject to allow access
     * @param array $verbs Optional list of allowed verbs.
     *     Empty or `null` means *all*.
     * @return Rule The allowing Rule
     */
    public static function allow(Subject $subject, array $verbs = null)
    {
        return new self($subject, true, $verbs);
    }
    
    /**
     * Creates an denying Rule.
     *
     * @param \Caridea\Acl\Subject $subject The subject to allow access
     * @param array $verbs Optional list of denied verbs.
     *     Empty or `null` means *all*.
     * @return Rule The denying Rule
     */
    public static function deny(Subject $subject, array $verbs = null)
    {
        return new self($subject, false, $verbs);
    }
}

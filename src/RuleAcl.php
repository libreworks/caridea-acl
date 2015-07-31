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
 * An immutable, serializable list of access rules.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class RuleAcl implements Acl
{
    /**
     * @var \Caridea\Acl\Acl The parent
     */
    private $parent;
    /**
     * @var \Caridea\Acl\Resource The resource
     */
    private $resource;
    /**
     * @var \Caridea\Acl\Subject[] The subjects for which the rules apply
     */
    private $subjects;
    /**
     * @var array Associative array of verb to list of Subjects granted access
     */
    private $rules = [];
    
    /**
     * Creates a new RuleAcl.
     *
     * @param \Caridea\Acl\Resource $resource The resource to which this applies
     * @param \Caridea\Acl\Subject[] $subjects An array of `Subject`s the rules cover
     * @param \Caridea\Acl\Rule[] $rules An array of `Rule`s
     * @param \Caridea\Acl\Acl $parent Optional parent ACL
     * @throws \InvalidArgumentException If subjects contains a non-Subject
     *     value, or if rules contains a non-Rule value
     */
    public function __construct(Resource $resource, array $subjects, array $rules, Acl $parent = null)
    {
        $this->resource = $resource;
        foreach ($subjects as $subject) {
            if (!($subject instanceof Subject)) {
                throw new \InvalidArgumentException("Only instances of Subject are permitted in the subjects argument");
            }
        }
        $this->subjects = $subjects;
        foreach ($rules as $rule) {
            if (!($rule instanceof Rule)) {
                throw new \InvalidArgumentException("Only instances of Rule are permitted in the rules argument");
            }
        }
        $this->rules = $rules;
        $this->parent = $parent;
    }
    
    /**
     * Determines whether this ACL has rules for all of the provided subjects.
     *
     * @param \Caridea\Acl\Subject[] $subjects a list of subjects
     * @return bool Whether all subjects are covered
     */
    protected function hasAllSubjects(array $subjects)
    {
        return count(array_diff($subjects, $this->subjects)) === 0;
    }
    
    /**
     * Tests whether any of the subjects can perform the provided verb.
     *
     * The parent ACL will be consulted if this one has no corresponding rules.
     *
     * @param \Caridea\Acl\Subject[] $subjects A non-empty array of subjects.
     * @param string $verb The verb to test.
     * @return bool True if a subject can perform a verb on this resource
     */
    public function can(array $subjects, $verb)
    {
        if (!$this->hasAllSubjects($subjects)) {
            throw new \InvalidArgumentException("This ACL does not apply to one of the provided subjects");
        }
        foreach ($this->rules as $rule) {
            foreach ($subjects as $subject) {
                if ($rule->match($subject, $verb)) {
                    return $rule->isAllowed();
                }
            }
        }
        return $this->parent ? $this->parent->can($subjects, $verb) : false;
    }

    /**
     * Gets the parent ACL.
     *
     * @return \Caridea\Acl\Acl The parent ACL or null
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * Gets the Resource for this ACL.
     *
     * @return \Caridea\Acl\Resource The resource for this ACL
     */
    public function getResource()
    {
        return $this->resource;
    }
}

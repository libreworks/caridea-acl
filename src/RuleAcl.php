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
 * An immutable, serializable list of access rules.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
class RuleAcl implements Acl
{
    /**
     * @var \Caridea\Acl\Acl The parent
     */
    private $parent;
    /**
     * @var \Caridea\Acl\Target The resource
     */
    private $target;
    /**
     * @var \Caridea\Acl\Subject[] The subjects for which the rules apply
     */
    private $subjects;
    /**
     * @var Rule[] The rules
     */
    private $rules = [];

    /**
     * Creates a new RuleAcl.
     *
     * @param \Caridea\Acl\Target $target The resource to which this applies
     * @param \Caridea\Acl\Subject[] $subjects An array of `Subject`s the rules cover
     * @param \Caridea\Acl\Rule[] $rules An array of `Rule`s
     * @param \Caridea\Acl\Acl|null $parent Optional parent ACL
     * @throws \InvalidArgumentException If subjects contains a non-Subject
     *     value, or if rules contains a non-Rule value
     */
    public function __construct(Target $target, array $subjects, array $rules, ?Acl $parent = null)
    {
        $this->target = $target;
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
    protected function hasAllSubjects(array $subjects): bool
    {
        return count(array_diff($subjects, $this->subjects)) === 0;
    }

    /**
     * {@inheritDoc}
     */
    public function can(array $subjects, string $verb): bool
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
     * {@inheritDoc}
     */
    public function getParent(): ?Acl
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function getTarget(): Target
    {
        return $this->target;
    }
}

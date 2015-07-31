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
 * Generated by PHPUnit_SkeletonGenerator on 2015-07-30 at 18:45:33.
 */
class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Caridea\Acl\Rule::match
     */
    public function testMatchNull()
    {
        $subject = Subject::role('admin');
        $object = Rule::allow($subject);
        $this->assertTrue($object->match($subject, 'create'));
        $this->assertTrue($object->match($subject, 'delete'));
        $this->assertFalse($object->match(Subject::principal('foobar'), 'test'));
        $this->assertFalse($object->match(Subject::role('user'), 'test'));
    }

    /**
     * @covers Caridea\Acl\Rule::match
     */
    public function testMatchArray()
    {
        $subject = Subject::role('admin');
        $object = Rule::allow($subject, ['create', 'read', 'update']);
        $this->assertTrue($object->match($subject, 'create'));
        $this->assertFalse($object->match($subject, 'delete'));
        $this->assertFalse($object->match(Subject::principal('foobar'), 'test'));
        $this->assertFalse($object->match(Subject::role('user'), 'test'));
    }

    /**
     * @covers Caridea\Acl\Rule::__construct
     * @covers Caridea\Acl\Rule::allow
     * @covers Caridea\Acl\Rule::isAllowed
     */
    public function testAllow()
    {
        $subject = Subject::role('admin');
        $object = Rule::allow($subject, ['create', 'read', 'update', 'delete']);
        $this->assertTrue($object->isAllowed());
    }

    /**
     * @covers Caridea\Acl\Rule::__construct
     * @covers Caridea\Acl\Rule::deny
     * @covers Caridea\Acl\Rule::isAllowed
     */
    public function testDeny()
    {
        $subject = Subject::role('admin');
        $object = Rule::deny($subject, ['create', 'read', 'update', 'delete']);
        $this->assertFalse($object->isAllowed());
    }
}

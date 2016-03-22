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
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
namespace Caridea\Acl;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-07-30 at 20:52:54.
 */
class RuleAclTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Caridea\Acl\RuleAcl::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only instances of Subject are permitted in the subjects argument
     */
    public function testConstructBad1()
    {
        new RuleAcl(new Target('foo', 'bar'), ['foo'], []);
    }
    
    /**
     * @covers Caridea\Acl\RuleAcl::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only instances of Rule are permitted in the rules argument
     */
    public function testConstructBad2()
    {
        new RuleAcl(new Target('foo', 'bar'), [], ['foo']);
    }
    
    /**
     * @covers Caridea\Acl\RuleAcl::can
     * @covers Caridea\Acl\RuleAcl::hasAllSubjects
     */
    public function testCan()
    {
        $target = new Target('foo', 'bar');
        $subjects = [Subject::role('admin')];
        $rules = [Rule::allow($subjects[0], ['read'])];
        $object = new RuleAcl($target, $subjects, $rules);
        
        $this->assertTrue($object->can($subjects, 'read'));
    }
    
    /**
     * @covers Caridea\Acl\RuleAcl::can
     * @covers Caridea\Acl\RuleAcl::hasAllSubjects
     */
    public function testCanParent()
    {
        $parent = $this->getMockForAbstractClass(Acl::class);
        $parent->expects($this->any())
            ->method('can')
            ->willReturn(true);
        
        $target = new Target('foo', 'bar');
        $subjects = [Subject::role('admin')];
        $rules = [];
        $object = new RuleAcl($target, $subjects, $rules, $parent);
        
        $this->assertTrue($object->can($subjects, 'read'));
    }
    
    /**
     * @covers Caridea\Acl\RuleAcl::can
     * @covers Caridea\Acl\RuleAcl::hasAllSubjects
     */
    public function testCanFalse()
    {
        $target = new Target('foo', 'bar');
        $subjects = [Subject::role('user')];
        $rules = [Rule::allow(Subject::role('admin'), ['update'])];
        $object = new RuleAcl($target, $subjects, $rules);
        
        $this->assertFalse($object->can($subjects, 'read'));
        $this->assertFalse($object->can([], 'read'));
    }
    
    /**
     * @covers Caridea\Acl\RuleAcl::can
     * @covers Caridea\Acl\RuleAcl::hasAllSubjects
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This ACL does not apply to one of the provided subjects
     */
    public function testCanSubjects()
    {
        $target = new Target('foo', 'bar');
        $subjects = [Subject::role('admin')];
        $rules = [];
        $object = new RuleAcl($target, $subjects, $rules);
        $object->can([Subject::principal('foobar')], 'delete');
    }
    
    /**
     * @covers Caridea\Acl\RuleAcl::__construct
     * @covers Caridea\Acl\RuleAcl::getParent
     */
    public function testGetParent()
    {
        $parent = $this->getMockForAbstractClass(Acl::class);
        
        $target = new Target('foo', 'bar');
        $subjects = [Subject::role('admin')];
        $rules = [Rule::allow($subjects[0], ['read'])];
        $object = new RuleAcl($target, $subjects, $rules, $parent);
        
        $this->assertSame($parent, $object->getParent());
    }

    /**
     * @covers Caridea\Acl\RuleAcl::__construct
     * @covers Caridea\Acl\RuleAcl::getTarget
     */
    public function testGetTarget()
    {
        $target = new Target('foo', 'bar');
        $subjects = [Subject::role('admin')];
        $rules = [Rule::allow($subjects[0], ['read'])];
        $object = new RuleAcl($target, $subjects, $rules);
        
        $this->assertSame($target, $object->getTarget());
    }
}

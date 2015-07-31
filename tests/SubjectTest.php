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
 * Generated by PHPUnit_SkeletonGenerator on 2015-07-30 at 18:51:42.
 */
class SubjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Caridea\Acl\Subject::__construct
     * @covers Caridea\Acl\Subject::getSubjectId
     * @covers Caridea\Acl\Subject::principal
     * @covers Caridea\Acl\Subject::getSubjectType
     * @covers Caridea\Acl\Subject::__toString
     */
    public function testPrincipal()
    {
        $id = 'foobar';
        $object = Subject::principal($id);
        $this->assertEquals(Subject::PRINCIPAL, $object->getSubjectType());
        $this->assertEquals($id, $object->getSubjectId());
        $this->assertEquals('principal:foobar', $object->__toString());
    }

    /**
     * @covers Caridea\Acl\Subject::__construct
     * @covers Caridea\Acl\Subject::getSubjectId
     * @covers Caridea\Acl\Subject::role
     * @covers Caridea\Acl\Subject::getSubjectType
     * @covers Caridea\Acl\Subject::__toString
     */
    public function testRole()
    {
        $id = 'foobar';
        $object = Subject::role($id);
        $this->assertEquals(Subject::ROLE, $object->getSubjectType());
        $this->assertEquals($id, $object->getSubjectId());
        $this->assertEquals('role:foobar', $object->__toString());
    }
}

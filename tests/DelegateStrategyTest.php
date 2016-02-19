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
 * Generated by PHPUnit_SkeletonGenerator on 2015-07-30 at 18:59:15.
 */
class DelegateStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Caridea\Acl\DelegateStrategy::__construct
     * @covers Caridea\Acl\DelegateStrategy::load
     */
    public function testLoad()
    {
        $service = $this->getMock(Service::class, [], [], '', false);
        $acl = $this->getMockForAbstractClass(Acl::class);
        $loader1 = $this->getMockForAbstractClass(Loader::class, ['load', 'supports']);
        $loader1->expects($this->any())
            ->method('supports')
            ->willReturn(false);
        $loader2 = $this->getMockForAbstractClass(Loader::class, ['load', 'supports']);
        $loader2->expects($this->any())
            ->method('supports')
            ->willReturn(true);
        $loader2->expects($this->any())
            ->method('load')
            ->willReturn($acl);
        
        $object = new DelegateStrategy([$loader1, $loader2]);
        
        $target = new Target('foo', 'bar');
        $subjects = [Subject::role('admin')];
        $this->assertSame($acl, $object->load($target, $subjects, $service));
        
        $this->verifyMockObjects();
    }
    
    /**
     * @covers Caridea\Acl\DelegateStrategy::__construct
     * @covers Caridea\Acl\DelegateStrategy::load
     */
    public function testLoadNoSupport()
    {
        $service = $this->getMock(Service::class, [], [], '', false);
        $loader1 = $this->getMockForAbstractClass(Loader::class, ['load', 'supports']);
        $loader1->expects($this->any())
            ->method('supports')
            ->willReturn(false);
        
        $object = new DelegateStrategy([$loader1]);
        
        $target = new Target('foo', 'bar');
        $subjects = [Subject::role('admin')];
        $this->assertInstanceOf(
            DenyAcl::class,
            $object->load($target, $subjects, $service)
        );
        
        $this->verifyMockObjects();
    }
    
    /**
     * @covers Caridea\Acl\DelegateStrategy::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only instances of Loader are permitted in the loaders argument
     */
    public function testConstructBad()
    {
        new DelegateStrategy(['foo']);
    }
}

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
        $service = $this->createMock(Service::class);
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
     * @covers Caridea\Acl\DelegateStrategy::loadAll
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only instances of Target are permitted in the targets argument
     */
    public function testLoadAllError()
    {
        $object = new DelegateStrategy([]);
        $service = $this->createMock(Service::class);
        $object->loadAll([1], [], $service);
    }

    /**
     * @covers Caridea\Acl\DelegateStrategy::__construct
     * @covers Caridea\Acl\DelegateStrategy::loadAll
     */
    public function testLoadAllSingle()
    {
        $service = $this->createMock(Service::class);
        $acl1 = $this->getMockForAbstractClass(Acl::class);
        $acl2 = $this->getMockForAbstractClass(Acl::class);
        $target1 = new Target('foo', 'bar');
        $target2 = new Target('foo', 'baz');
        $targets = [$target1, $target2];
        $subjects = [Subject::role('admin')];

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
            ->willReturnOnConsecutiveCalls($acl1, $acl2);

        $acls = ['foo#bar' => $acl1, 'foo#baz' => $acl2];

        $object = new DelegateStrategy([$loader1, $loader2]);

        $this->assertEquals($acls, $object->loadAll($targets, $subjects, $service));

        $this->verifyMockObjects();
    }

    /**
     * @covers Caridea\Acl\DelegateStrategy::__construct
     * @covers Caridea\Acl\DelegateStrategy::loadAll
     */
    public function testLoadAllMulti()
    {
        $service = $this->createMock(Service::class);
        $acl1 = $this->getMockForAbstractClass(Acl::class);
        $acl2 = $this->getMockForAbstractClass(Acl::class);
        $target1 = new Target('foo', 'bar');
        $target2 = new Target('foo', 'baz');
        $targets = [$target1, $target2];
        $subjects = [Subject::role('admin')];
        $acls = ['foo#bar' => $acl1, 'foo#baz' => $acl2];

        $loader1 = $this->getMockForAbstractClass(Loader::class, ['load', 'supports']);
        $loader1->expects($this->any())
            ->method('supports')
            ->willReturn(false);
        $loader2 = $this->createMock(DelegateStrategyTest_MultiLoader::class);
        $loader2->expects($this->any())
            ->method('supports')
            ->willReturn(true);
        $loader2->expects($this->any())
            ->method('loadAll')
            ->willReturn($acls);

        $object = new DelegateStrategy([$loader1, $loader2]);

        $this->assertEquals($acls, $object->loadAll($targets, $subjects, $service));

        $this->verifyMockObjects();
    }

    /**
     * @covers Caridea\Acl\DelegateStrategy::__construct
     * @covers Caridea\Acl\DelegateStrategy::load
     */
    public function testLoadNoSupport()
    {
        $service = $this->createMock(Service::class);
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

interface DelegateStrategyTest_MultiLoader extends Loader, MultiStrategy
{
}
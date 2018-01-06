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
 * Generated by PHPUnit_SkeletonGenerator on 2015-07-30 at 19:51:34.
 */
class CacheStrategyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Caridea\Acl\CacheStrategy::__construct
     * @covers Caridea\Acl\CacheStrategy::load
     * @covers Caridea\Acl\CacheStrategy::buildKey
     */
    public function testLoad()
    {
        $service = $this->createMock(Service::class);
        $acl = $this->getMockForAbstractClass(Acl::class);
        $delegate = $this->getMockForAbstractClass(Strategy::class);
        $delegate->expects($this->once())
            ->method('load')
            ->willReturn($acl);

        $object = new CacheStrategy($delegate);
        $target = new Target('foo', 'bar');
        $subjects = [Subject::role('admin')];

        $this->assertSame($acl, $object->load($target, $subjects, $service));
        $this->assertSame($acl, $object->load($target, $subjects, $service));

        $this->verifyMockObjects();
    }

    /**
     * @covers Caridea\Acl\CacheStrategy::__construct
     * @covers Caridea\Acl\CacheStrategy::load
     * @covers Caridea\Acl\CacheStrategy::buildKey
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only instances of Subject are permitted in the subjects argument
     */
    public function testLoadBad()
    {
        $delegate = $this->getMockForAbstractClass(Strategy::class);
        $service = $this->createMock(Service::class);

        $object = new CacheStrategy($delegate);
        $target = new Target('foo', 'bar');
        $subjects = ['not-a-subject'];

        $object->load($target, $subjects, $service);
    }

    /**
     * @covers Caridea\Acl\CacheStrategy::__construct
     * @covers Caridea\Acl\CacheStrategy::load
     * @covers Caridea\Acl\CacheStrategy::loadAll
     * @covers Caridea\Acl\CacheStrategy::buildKey
     */
    public function testLoadAllSingle()
    {
        $delegate = $this->getMockForAbstractClass(Strategy::class);
        $service = $this->createMock(Service::class);

        $target1 = new Target('foo', 'bar');
        $target2 = new Target('foo', 'baz');
        $acl1 = $this->createMock(Acl::class);
        $acl2 = $this->createMock(Acl::class);
        $targets = [$target1, $target2];
        $subjects = [Subject::role('admin')];
        $object = new CacheStrategy($delegate);

        $acls = ['foo#bar' => $acl1, 'foo#baz' => $acl2];

        $delegate->expects($this->any())
            ->method('load')
            ->willReturnOnConsecutiveCalls($acl1, $acl2);

        $this->assertEquals($acls, $object->loadAll($targets, $subjects, $service));

        $this->verifyMockObjects();
    }

    /**
     * @covers Caridea\Acl\CacheStrategy::__construct
     * @covers Caridea\Acl\CacheStrategy::loadAll
     * @covers Caridea\Acl\CacheStrategy::buildKey
     */
    public function testLoadAllMulti()
    {
        $delegate = $this->getMockForAbstractClass(MultiStrategy::class);
        $service = $this->createMock(Service::class);

        $target1 = new Target('foo', 'bar');
        $target2 = new Target('foo', 'baz');
        $acl1 = $this->createMock(Acl::class);
        $acl1->method('getTarget')
            ->willReturn($target1);
        $acl2 = $this->createMock(Acl::class);
        $acl2->method('getTarget')
            ->willReturn($target2);
        $targets = [$target1, $target2];
        $subjects = [Subject::role('admin')];
        $object = new CacheStrategy($delegate);

        $acls = ['foo#bar' => $acl1, 'foo#baz' => $acl2];

        $delegate->expects($this->any())
            ->method('loadAll')
            ->willReturn(['foo#baz' => $acl2]);

        $rc = new \ReflectionClass(CacheStrategy::class);
        $rp = $rc->getProperty('cache');
        $rp->setAccessible(true);
        $rp->setValue($object, ['foo#bar;role:admin' => $acl1]);

        $this->assertEquals($acls, $object->loadAll($targets, $subjects, $service));

        $this->verifyMockObjects();
    }

    /**
     * @covers Caridea\Acl\CacheStrategy::__construct
     * @covers Caridea\Acl\CacheStrategy::loadAll
     * @covers Caridea\Acl\CacheStrategy::buildKey
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only instances of Target are permitted in the targets argument
     */
    public function testLoadAllBad1()
    {
        $delegate = $this->getMockForAbstractClass(Strategy::class);
        $service = $this->createMock(Service::class);
        $object = new CacheStrategy($delegate);
        $object->loadAll([1], [], $service);
    }

    /**
     * @covers Caridea\Acl\CacheStrategy::__construct
     * @covers Caridea\Acl\CacheStrategy::loadAll
     * @covers Caridea\Acl\CacheStrategy::buildKey
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only instances of Target are permitted in the targets argument
     */
    public function testLoadAllBad2()
    {
        $delegate = $this->getMockForAbstractClass(MultiStrategy::class);
        $service = $this->createMock(Service::class);
        $object = new CacheStrategy($delegate);
        $object->loadAll([1], [], $service);
    }
}

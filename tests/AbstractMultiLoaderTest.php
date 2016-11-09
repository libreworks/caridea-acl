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
 * Generated by hand.
 */
class AbstractMultiLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Caridea\Acl\AbstractMultiLoader::load
     */
    public function testLoad()
    {
        $target = new Target('foo', 'bar');
        $subjects = [Subject::role('admin')];

        $acl = $this->getMockForAbstractClass(Acl::class);
        $service = $this->getMockBuilder(Service::class)
            ->disableOriginalConstructor()
            ->getMock();
        $loader = $this->getMockBuilder(AbstractMultiLoader::class)
            ->setMethods(['loadAll', 'supports'])
            ->getMock();
        $loader->expects($this->never())
            ->method('supports');
        $loader->expects($this->once())
            ->method('loadAll')
            ->with(
                $this->equalTo([$target]),
                $this->equalTo($subjects),
                $this->equalTo($service)
            )->willReturnCallback(function ($targets, $subs, $svc) use ($acl, $target) {
                return [
                    ((string) $target) => $acl,
                ];
            });

        $acl2 = $loader->load($target, $subjects, $service);
        $this->assertSame($acl, $acl2);

        $this->verifyMockObjects();
    }
}
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
 * Abstract base for `Loaders` which implement `MultiStrategy`
 *
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
abstract class AbstractMultiLoader implements Loader, MultiStrategy
{
    /**
     * {@inheritDoc}
     */
    public function load(Target $target, array $subjects, Service $service): Acl
    {
        $acls = $this->loadAll([$target], $subjects, $service);
        if (!array_key_exists((string) $target, $acls)) {
            throw new Exception\Unloadable("Could not load ACL for $target");
        }
        return $acls[(string) $target];
    }
}

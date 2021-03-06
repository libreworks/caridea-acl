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
 * A strategy for loading specific ACLs.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
interface Loader extends Strategy
{
    /**
     * Whether this loader supports the Target provided.
     *
     * Implementations should only worry about the Target type. They shouldn't
     * actually check to see if the Target ID is valid.
     *
     * @param \Caridea\Acl\Target $target A target
     * @return bool Whether the Target type is supported
     */
    public function supports(Target $target): bool;
}

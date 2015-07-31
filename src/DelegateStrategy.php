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
 * Delegates ACL retrieval to Loaders.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class DelegateStrategy implements Strategy
{
    /**
     * @var SplFixedArray<Loader> The loaders to consult
     */
    private $loaders;

    /**
     * Creates a new DelegateStrategy.
     *
     * @param Loader[] $loaders An array of Loaders to use
     * @throws \InvalidArgumentException if an entry in `loaders` isn't a `Loader`
     */
    public function __construct(array $loaders)
    {
        foreach ($loaders as $loader) {
            if (!($loader instanceof Loader)) {
                throw new \InvalidArgumentException("Only instances of Loader are permitted in the loaders argument");
            }
        }
        $this->loaders = \SplFixedArray::fromArray($loaders);
    }

    /**
     * Loads the ACL for a Resource.
     *
     * @param Resource $resource The `Resource` whose ACL will be loaded
     * @param Subject[] $subjects An array of `Subject`s
     * @param Service $service The calling service (to load parent ACLs)
     * @return Acl The loaded ACL
     * @throws Exception\Unloadable If the resource provided is invalid
     */
    public function load(Resource $resource, array $subjects, Service $service)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($resource)) {
                return $loader->load($resource, $subjects, $service);
            }
        }
        return new DenyAcl($resource);
    }
}

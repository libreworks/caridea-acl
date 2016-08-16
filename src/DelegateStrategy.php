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
 * Delegates ACL retrieval to Loaders.
 *
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class DelegateStrategy implements MultiStrategy
{
    /**
     * @var SplFixedArray<\Caridea\Acl\Loader> The loaders to consult
     */
    private $loaders;

    /**
     * Creates a new DelegateStrategy.
     *
     * @param \Caridea\Acl\Loader[] $loaders An array of Loaders to use
     * @throws \InvalidArgumentException if an entry in `loaders` isn't a `Loader`
     */
    public function __construct(array $loaders)
    {
        foreach ($loaders as $loader) {
            if (!($loader instanceof Loader)) {
                throw new \InvalidArgumentException("Only instances of Loader are permitted in the loaders argument");
            }
        }
        // HHVM appears to use max() inside of FixedArray which bails when empty
        $this->loaders = empty($loaders) ?
            new \SplFixedArray() : \SplFixedArray::fromArray($loaders);
    }

    /**
     * Loads the ACL for a Target.
     *
     * @param \Caridea\Acl\Target $target The `Target` whose ACL will be loaded
     * @param \Caridea\Acl\Subject[] $subjects An array of `Subject`s
     * @param \Caridea\Acl\Service $service The calling service (to load parent ACLs)
     * @return \Caridea\Acl\Acl The loaded ACL
     * @throws \Caridea\Acl\Exception\Unloadable If the target provided is invalid
     */
    public function load(Target $target, array $subjects, Service $service): Acl
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($target)) {
                return $loader->load($target, $subjects, $service);
            }
        }
        return new DenyAcl($target);
    }

    /**
     * Loads the ACLs for several Targets.
     *
     * @since 2.1.0
     * @param \Caridea\Acl\Target[] $targets The `Target` whose ACL will be loaded
     * @param \Caridea\Acl\Subject[] $subjects An array of `Subject`s
     * @param \Caridea\Acl\Service $service The ACL service (to load parent ACLs)
     * @return array<string,\Caridea\Acl\Acl> The loaded ACLs
     * @throws \Caridea\Acl\Exception\Unloadable If the target provided is invalid
     * @throws \InvalidArgumentException If the `subjects` argument contains invalid values
     */
    public function loadAll(array $targets, array $subjects, Service $service): array
    {
        $byType = [];
        foreach ($targets as $target) {
            if (!($target instanceof Target)) {
                throw new \InvalidArgumentException("Only instances of Target are permitted in the targets argument");
            }
            $type = $target->getType();
            if (!isset($byType[$type])) {
                $byType[$type] = [];
            }
            $byType[$type][] = $target;
        }
        $acls = [];
        foreach ($byType as $type => $ttargets) {
            foreach ($this->loaders as $loader) {
                if ($loader->supports($ttargets[0])) {
                    if ($loader instanceof MultiStrategy) {
                        $acls = array_merge($acls, $loader->loadAll($ttargets, $subjects, $service));
                    } else {
                        foreach ($ttargets as $target) {
                            $acls[(string)$target] = $loader->load($target, $subjects, $service);
                        }
                    }
                    break;
                }
            }
        }
        return $acls;
    }
}

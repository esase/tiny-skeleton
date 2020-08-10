<?php

namespace Tiny\Skeleton;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\ServiceManager;

class Bootstrap
{

    const CONFIG_FILE = 'config.php';

    /**
     * @param  array  $modules
     *
     * @return array
     */
    public function loadModulesConfigs(array $modules)
    {
        $configs = [];

        foreach ($modules as $module) {
            $moduleConfigPath = vsprintf('%s/Module/%s/%s', [
                __DIR__,
                basename($module),
                self::CONFIG_FILE
            ]);

            $configs = array_merge($configs,
                require_once $moduleConfigPath
            );
        }

        return $configs;
    }

    /**
     * @param  array  $configs
     *
     * @return ServiceManager
     */
    public function initServiceManager(array $configs): ServiceManager
    {
        return new ServiceManager(
            ($configs['service_manager']['shared'] ?? []),
            ($configs['service_manager']['discrete'] ?? [])
        );
    }

}

<?php

namespace Tiny\Skeleton\Core;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class BootstrapUtils
{

    const CONFIG_FILE = 'config.php';

    /**
     * @var string
     */
    private string $projectRootDir;

    /**
     * Bootstrap constructor.
     *
     * @param  string  $projectRootDir
     */
    public function __construct(string $projectRootDir)
    {
        $this->projectRootDir = $projectRootDir;
    }

    /**
     * @return array|null
     */
    public function loadCachedModulesConfigArray()
    {
        $configPath = $this->getCachedModulesConfigArrayPath();

        if (file_exists($configPath)) {
            return require_once $configPath;
        }
    }

    /**
     * @param  array  $configs
     */
    public function saveCachedModulesConfigArray(array $configs)
    {
        file_put_contents(
            $this->getCachedModulesConfigArrayPath(),
            '<?php return ' . var_export($configs, true) . ';'
        );
    }

    /**
     * @param  string  $module
     *
     * @return array
     */
    public function loadModuleConfigArray(string $module): array
    {
        $configPath = vsprintf('%s/src/Module/%s/%s', [
            $this->projectRootDir,
            basename($module),
            self::CONFIG_FILE
        ]);

        return require_once $configPath;
    }

    /**
     * @return string
     */
    private function getCachedModulesConfigArrayPath(): string
    {
        return vsprintf('%s/data/config/%s', [
            $this->projectRootDir,
            self::CONFIG_FILE
        ]);
    }

}

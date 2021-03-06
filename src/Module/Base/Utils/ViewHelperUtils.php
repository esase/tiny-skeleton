<?php

namespace Tiny\Skeleton\Module\Base\Utils;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Application\Service\ConfigService;

class ViewHelperUtils
{

    /**
     * @var ConfigService
     */
    private ConfigService $configService;

    /**
     * ViewHelperConfigListener constructor.
     *
     * @param  ConfigService  $configService
     */
    public function __construct(
        ConfigService $configService
    ) {
        $this->configService = $configService;
    }

    /**
     * @param  string  $template
     * @param  string  $module
     *
     * @return string
     */
    public function getTemplatePath(
        string $template,
        string $module
    ): string {
        $viewConfig = $this->configService->getConfig('view');

        return vsprintf(
            '%s/%s/view/%s.%s', [
                $this->configService->getConfig('modules_root'),
                $module,
                $template,
                $viewConfig['template_extension'],
            ]
        );
    }

    /**
     * @param  string  $controller
     * @param  string  $module
     *
     * @return string
     */
    public function getControllerPath(
        string $controller,
        string $module
    ): string {
        return vsprintf(
            'Tiny\Skeleton\Module\%s\Controller\%s', [
                $module,
                $controller,
            ]
        );
    }

    /**
     * @param  string  $className
     *
     * @return string
     */
    public function extractModuleName(string $className): string
    {
        list(, , , $moduleName) = explode('\\', $className);

        return $moduleName;
    }

}

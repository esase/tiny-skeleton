<?php

namespace Tiny\Skeleton\Module\Core\Utils;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Skeleton\Module\Core;

class ViewHelperUtilsTest extends TestCase
{

    public function testGetTemplatePathMethod()
    {
        $configServiceMock = $this->createMock(
            ConfigService::class
        );
        $configServiceMock->expects($this->exactly(2))
            ->method('getConfig')
            ->withConsecutive(
                ['view'],
                ['modules_root']
            )
            ->will(
                $this->returnCallback(
                    function (string $configName) {
                        switch ($configName) {
                            case 'view':
                                return [
                                    'template_extension' => 'phtml'
                                ];

                            case 'modules_root':
                                return 'test_root';

                            default :
                                return null;
                        }
                    }
                )
            );

        $utils = new ViewHelperUtils($configServiceMock);
        $templatePath = $utils->getTemplatePath(
            'partial/user',
            'User'
        );

        $this->assertEquals(
            'test_root/User/view/partial/user.phtml',
            $templatePath
        );
    }

    public function testGetControllerPathMethod()
    {
        $utils = new ViewHelperUtils($this->createStub(
            ConfigService::class
        ));
        $controllerPath = $utils->getControllerPath(
            'TestController',
            'Test'
        );

        $this->assertEquals(
            'Tiny\Skeleton\Module\Test\Controller\TestController',
            $controllerPath
        );
    }

    public function testExtractModuleNameMethod()
    {
        $utils = new ViewHelperUtils($this->createStub(
            ConfigService::class
        ));
        $moduleName = $utils->extractModuleName(
            'Tiny\Skeleton\Module\Test\Controller\TestController'
        );

        $this->assertEquals(
            'Test',
            $moduleName
        );
    }

}

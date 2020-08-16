<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tiny\Skeleton;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use Tiny\Skeleton\Module\Core;

class BootstrapperUtilsTest extends TestCase
{

    use PHPMock;

    /**
     * @var BootstrapperUtils
     */
    protected BootstrapperUtils $utils;

    protected function setUp(): void
    {
        $this->utils = new BootstrapperUtils(
            __DIR__.'/fixtures'
        );
    }

    public function testLoadCachedModulesConfigArrayMethod()
    {
        $config = $this->utils->loadCachedModulesConfigArray();

        $this->assertEquals(
            [
                'test' => 'test_value',
            ], $config
        );
    }

    public function testSaveCachedModulesConfigArrayMethod()
    {
        $varExport = $this->getFunctionMock(
            __NAMESPACE__,
            'var_export'
        );
        $varExport->expects($this->once())->with(
            [],
            true
        )->willReturn('[]');

        $filePutContents = $this->getFunctionMock(
            __NAMESPACE__,
            'file_put_contents'
        );
        $filePutContents->expects($this->once())->with(
            $this->stringContains('data/config/config.php'),
            '<?php return [];'
        );

        $this->utils->saveCachedModulesConfigArray([]);
    }

    public function testLoadModuleConfigArrayMethod()
    {
        $config = $this->utils->loadModuleConfigArray('Test');

        $this->assertEquals(
            [
                'test' => 'test_value',
            ], $config
        );
    }

}

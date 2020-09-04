<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tiny\Skeleton\Application;

use Exception;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Tiny\View\View;

class ErrorHandlerTest extends TestCase
{

    use PHPMock;

    public function testLogErrorMethodUsingDevEnvironment()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Test error'
        );

        $errorHandler = new ErrorHandler(
            false,
            false,
            '',
            ''
        );

        $errorHandler->logError(
            new Exception('Test error')
        );
    }

    public function testLogErrorMethodUsingProdEnvironmentAndHttpContext()
    {
        $exception = new Exception();

        $exceptionReflection = new ReflectionObject($exception);

        $traceReflection = $exceptionReflection->getProperty('message');
        $traceReflection->setAccessible(true);
        $traceReflection->setValue($exception, 'test_error_message');

        $traceReflection = $exceptionReflection->getProperty('file');
        $traceReflection->setAccessible(true);
        $traceReflection->setValue($exception, 'test_file');

        $traceReflection = $exceptionReflection->getProperty('line');
        $traceReflection->setAccessible(true);
        $traceReflection->setValue($exception, 'test_line');

        $traceReflection = $exceptionReflection->getProperty('trace');
        $traceReflection->setAccessible(true);
        $traceReflection->setValue($exception, []);

        $errorHandler = new ErrorHandler(
            true,
            false,
            'error_template_path',
            'log_path'
        );

        $date = $this->getFunctionMock(
            __NAMESPACE__,
            'date'
        );
        $date->expects($this->once())->willReturn('test_date');

        $jsonEncode = $this->getFunctionMock(
            __NAMESPACE__,
            'json_encode'
        );
        $jsonEncode->expects($this->once())->with(
            [
                'date'    => 'test_date',
                'message' => 'test_error_message',
                'file'    => 'test_file',
                'line'    => 'test_line',
                'trace'   => [],
            ]
        )
        ->willReturn('test_log');

        $filePutContents = $this->getFunctionMock(
            __NAMESPACE__,
            'file_put_contents'
        );
        $filePutContents->expects($this->once())->with(
            'log_path',
            'test_log' . "\n",
            FILE_APPEND
        );

        /** @var View $result */
        $view = $errorHandler->logError(
            $exception
        );

        $this->assertInstanceOf(
            View::class,
            $view
        );

        $this->assertSame($exception, $view->getVariables()['error']);

        $this->assertEquals('error_template_path', $view->getTemplatePath());
    }

}

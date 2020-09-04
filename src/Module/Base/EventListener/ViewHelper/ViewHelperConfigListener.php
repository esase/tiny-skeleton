<?php

namespace Tiny\Skeleton\Module\Base\EventListener\ViewHelper;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\Event;
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Skeleton\Module\Base;

class ViewHelperConfigListener
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
     * @param  Event  $event
     */
    public function __invoke(Event $event)
    {
        $arguments = $event->getParams()['arguments'];
        list($configName) = $arguments;

        $event->setData(
            $this->configService->getConfig($configName)
        );
    }

}

<?php

namespace Tiny\Skeleton\Module\Core\EventListener\ViewHelper;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\Event;
use Tiny\Skeleton\Module\Core;

class ViewHelperConfigListener
{

    /**
     * @var Core\Service\ConfigService
     */
    private Core\Service\ConfigService $configService;

    /**
     * ViewHelperConfigListener constructor.
     *
     * @param  Core\Service\ConfigService  $configService
     */
    public function __construct(
        Core\Service\ConfigService $configService
    ) {
        $this->configService = $configService;
    }

    /**
     * @param  Event  $event
     */
    public function __invoke(Event $event)
    {
        $arguments = $event->getParams()['arguments'];

        $event->setData(
            $this->configService->getConfig($arguments[0])
        );
    }

}

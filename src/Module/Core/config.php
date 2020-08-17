<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'site' => [
        'name' => 'Test site'
    ],
    'view'            => [
        'base_layout_path'   => __DIR__.'/view/layout/base.phtml',
        'template_path_mask' => '{module}/view/{controller_name}/{action}.phtml',
    ],
    'service_manager' => require_once 'config/service-manager.php',
    'listeners'       => require_once 'config/listeners.php',
];

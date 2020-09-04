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
    'modules_root' => dirname(__DIR__),
    'view'            => [
        'base_layout_path'   => 'layout/base',
        'template_extension' => 'phtml',
    ],
    'service_manager' => require_once 'config/service-manager.php',
    'listeners'       => require_once 'config/listeners.php',
];

<?php

namespace Tiny\Skeleton\Application\Service;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Application\Exception\InvalidArgumentException;

class ConfigService
{

    /**
     * @var array
     */
    private array $configs;

    /**
     * @param  array  $configs
     */
    public function setConfigs(array $configs)
    {
        $this->configs = $configs;
    }

    /**
     * @param  string      $name
     * @param  mixed|null  $defaultValue
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getConfig(string $name, $defaultValue = null)
    {
        if (isset($this->configs[$name])) {
            return $this->configs[$name];
        }

        if (null !== $defaultValue) {
            return $defaultValue;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Unknown config "%s"',
                $name
            )
        );
    }

}

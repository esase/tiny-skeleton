<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tiny\Skeleton\Application\Exception\Request;

use Exception;

class BaseException extends Exception
{

    const TYPE_JSON = 'json';

    const TYPE_HTML = 'html';

    const TYPE_TEXT = 'text';

    /**
     * @var string
     */
    protected string $type;

    /**
     * BaseException constructor.
     *
     * @param  string  $type
     * @param  string  $message
     */
    public function __construct(
        string $type = self::TYPE_HTML,
        string $message = ''
    ) {
        parent::__construct($message, 0, null);

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

}

<?php

namespace Tiny\Skeleton\Module\Base\Controller;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Http\AbstractResponse;

class NotFoundController extends AbstractController
{

    /**
     * @param  AbstractResponse  $response
     */
    public function index(AbstractResponse $response)
    {
        $this->viewResponse(
            $response, [], AbstractResponse::RESPONSE_NOT_FOUND
        );
    }

}

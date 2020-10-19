<?php

namespace Tiny\Skeleton\Module\Base\Service;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PDO;
use Tiny\Skeleton\Application\Service\DbService;

class AuthService
{

    const PERMISSION_READ = 'read';
    const PERMISSION_WRITE = 'write';

    /**
     * @var DbService
     */
    private DbService $dbService;

    public function __construct(DbService $dbService)
    {
        $this->dbService = $dbService;
    }

    public function getTokenData(string $token)
    {
        $sth = $this->dbService->getConnection()->prepare(
            'SELECT * FROM tokens WHERE `key` = :token'
        );
        $sth->bindValue(':token', $token, PDO::PARAM_STR);
        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

}

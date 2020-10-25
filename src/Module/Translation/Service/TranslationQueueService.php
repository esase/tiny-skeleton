<?php

namespace Tiny\Skeleton\Module\Translation\Service;

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

class TranslationQueueService
{

    /**
     * @var DbService
     */
    private DbService $dbService;

    /**
     * TranslationQueueService constructor.
     *
     * @param DbService $dbService
     */
    public function __construct(DbService $dbService)
    {
        $this->dbService = $dbService;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function findLimited(int $limit): array
    {
        $sth = $this->dbService->getConnection()->prepare(
            'SELECT * from `translations_queue` ORDER BY `created` LIMIT :limit'
        );
        $sth->bindValue(':limit', $limit, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $languageKey
     */
    public function create(int $languageKey)
    {
        if (!$this->findOneByLanguageKey($languageKey)) {
            $sth = $this->dbService->getConnection()->prepare(
                '
                INSERT INTO
                    `translations_queue` 
                SET 
                    `language_key` = :language_key, 
                    `created` = UNIX_TIMESTAMP()
            '
            );
            $sth->bindValue(':language_key', $languageKey, PDO::PARAM_STR);
            $sth->execute();
        }
    }

    /**
     * @param int $languageKey
     *
     * @return array|bool
     */
    public function findOneByLanguageKey(int $languageKey)
    {
        $sth = $this->dbService->getConnection()->prepare(
            'SELECT * FROM `translations_queue` WHERE `language_key` = :language_key'
        );
        $sth->bindValue(':language_key', $languageKey, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $id
     */
    public function deleteOne(int $id)
    {
        $sth = $this->dbService->getConnection()->prepare(
            'DELETE FROM `translations_queue` WHERE `id` = :id'
        );
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
    }

}

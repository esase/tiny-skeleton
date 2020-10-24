<?php

namespace Tiny\Skeleton\Module\LanguageKey\Service;

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

class LanguageKeyService
{

    /**
     * @var DbService
     */
    private DbService $dbService;

    public function __construct(DbService $dbService)
    {
        $this->dbService = $dbService;
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $sth = $this->dbService->getConnection()->prepare(
            'SELECT * from `language_keys`'
        );
        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $id
     *
     * @return array|bool
     */
    public function findOne(int $id)
    {
        $sth = $this->dbService->getConnection()->prepare(
            'SELECT * FROM `language_keys` WHERE `id` = :id'
        );
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $key
     * @param int    $languageKeyId
     *
     * @return bool
     */
    public function isLanguageKeyExist(string $key, int $languageKeyId)
    {
        $sth = $this->dbService->getConnection()->prepare(
            'SELECT * FROM `language_keys` WHERE `name` = :key AND `id` <> :id'
        );
        $sth->bindValue(':key', $key, PDO::PARAM_STR);
        $sth->bindValue(':id', $languageKeyId ?? -1, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    /**
     * @param int $id
     */
    public function deleteOne(int $id)
    {
        $sth = $this->dbService->getConnection()->prepare(
            'DELETE FROM `language_keys` WHERE `id` = :id'
        );
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
    }

    /**
     * @param string $name
     *
     * @return int
     */
    public function create(string $name): int
    {
        $sth = $this->dbService->getConnection()->prepare(
            'INSERT INTO `language_keys` SET `name` = :name'
        );
        $sth->bindValue(':name', $name, PDO::PARAM_STR);
        $sth->execute();

        return (int)$this->dbService->getConnection()->lastInsertId();
    }

    /**
     * @param int    $id
     * @param string $name
     *
     * @return bool
     */
    public function update(int $id, string $name): bool
    {
        $sth = $this->dbService->getConnection()->prepare(
            'UPDATE `language_keys` SET `name` = :name WHERE `id` = :id'
        );
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->bindValue(':name', $name, PDO::PARAM_STR);

        return $sth->execute();
    }

}

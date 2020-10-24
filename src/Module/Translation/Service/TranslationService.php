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

class TranslationService
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
            'SELECT * from `translations`'
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
            'SELECT * FROM `translations` WHERE `id` = :id'
        );
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int      $languageKeyId
     * @param int      $languageId
     * @param int|null $translationId
     *
     * @return bool
     */
    public function isTranslationExist(
        int $languageKeyId,
        int $languageId,
        int $translationId = null
    ) {
        $sth = $this->dbService->getConnection()->prepare(
            'SELECT * FROM `translations` 
            WHERE 
                `language_key` = :language_key 
                    AND
                `language` = :language AND `id` <> :id'
        );
        $sth->bindValue(':language_key', $languageKeyId, PDO::PARAM_INT);
        $sth->bindValue(':language', $languageId, PDO::PARAM_INT);
        $sth->bindValue(':id', $translationId ?? -1, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    /**
     * @param int $id
     */
    public function deleteOne(int $id)
    {
        $sth = $this->dbService->getConnection()->prepare(
            'DELETE FROM `translations` WHERE `id` = :id'
        );
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
    }

    /**
     * @param int    $languageKey
     * @param int    $language
     * @param string $translation
     *
     * @return int
     */
    public function create(
        int $languageKey,
        int $language,
        string $translation
    ): int {
        $sth = $this->dbService->getConnection()->prepare(
            'INSERT INTO `translations` 
            SET 
                `language_key` = :language_key, 
                `language` = :language, 
                `translation` = :translation
        '
        );
        $sth->bindValue(':language_key', $languageKey, PDO::PARAM_INT);
        $sth->bindValue(':language', $language, PDO::PARAM_INT);
        $sth->bindValue(':translation', $translation, PDO::PARAM_STR);
        $sth->execute();

        return (int)$this->dbService->getConnection()->lastInsertId();
    }

    /**
     * @param int    $id
     * @param int    $languageKey
     * @param int    $language
     * @param string $translation
     *
     * @return bool
     */
    public function update(
        int $id,
        int $languageKey,
        int $language,
        string $translation
    ): bool {
        $sth = $this->dbService->getConnection()->prepare(
            'UPDATE `translations` 
                SET 
                    `language_key` = :language_key,
                     `language` = :language,
                     `translation` = :translation
                WHERE `id` = :id'
        );
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->bindValue(':language_key', $languageKey, PDO::PARAM_INT);
        $sth->bindValue(':language', $language, PDO::PARAM_INT);
        $sth->bindValue(':translation', $translation, PDO::PARAM_STR);

        return $sth->execute();
    }

}

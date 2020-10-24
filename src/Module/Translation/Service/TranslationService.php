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
     * @param bool   $isAutomatic
     *
     * @return int
     */
    public function create(
        int $languageKey,
        int $language,
        string $translation,
        bool $isAutomatic = false
    ): int {
        $sth = $this->dbService->getConnection()->prepare(
            'INSERT INTO `translations` 
            SET 
                `language_key` = :language_key, 
                `language` = :language, 
                `translation` = :translation,
                `automatic` = :automatic
        '
        );
        $sth->bindValue(':language_key', $languageKey, PDO::PARAM_INT);
        $sth->bindValue(':language', $language, PDO::PARAM_INT);
        $sth->bindValue(':translation', $translation, PDO::PARAM_STR);
        $sth->bindValue(':automatic', $isAutomatic ? 1 : 0, PDO::PARAM_INT);
        $sth->execute();

        $translationId = (int)$this->dbService->getConnection()->lastInsertId();

        // add the language key in the queue (for adding auto translations)
        if (!$isAutomatic) {
            $this->addLanguageKeyToQueue($languageKey);
        }

        return $translationId;
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

        $result = $sth->execute();

        // add the language key in the queue (for adding auto translations)
        $this->addLanguageKeyToQueue($languageKey);

        return $result;
    }

    /**
     * @param int $languageKey
     */
    private function addLanguageKeyToQueue(int $languageKey)
    {
        if (!$this->findOneInQueue($languageKey)) {
            $sth = $this->dbService->getConnection()->prepare(
                'INSERT INTO `translations_queue` SET `language_key` = :language_key, `created` = UNIX_TIMESTAMP()'
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
    public function findOneInQueue(int $languageKey)
    {
        $sth = $this->dbService->getConnection()->prepare(
            'SELECT * FROM `translations_queue` WHERE `language_key` = :language_key'
        );
        $sth->bindValue(':language_key', $languageKey, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function findQueuedItems(int $limit): array
    {
        $sth = $this->dbService->getConnection()->prepare(
            'SELECT * from `translations_queue` ORDER BY `created` LIMIT :limit'
        );
        $sth->bindValue(':limit', $limit, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $id
     */
    public function deleteQueuedItem(int $id)
    {
        $sth = $this->dbService->getConnection()->prepare(
            'DELETE FROM `translations_queue` WHERE `id` = :id'
        );
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
    }

    /**
     * @param int $languageKey
     *
     * @return array
     */
    public function findAllTranslations(int $languageKey): array
    {
        $sth = $this->dbService->getConnection()->prepare(
            '
            SELECT 
                `a`.`id` AS `languageId`,
                `a`.`iso` AS `languageIso`,
                `b`.`translation`,
                `b`.`automatic`
            FROM 
                `languages` AS `a`
            LEFT JOIN
                `translations` AS `b`
            ON 
                `a`.`id` = `b`.`language`   
                    AND
                `b`.`language_key` = :language_key
        '
        );
        $sth->bindValue(':language_key', $languageKey, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

}

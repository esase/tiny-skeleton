<?php

namespace Tiny\Skeleton\Application\Utility;

use Tiny\Skeleton\Application\Exception\InvalidArgumentException;
use ZipArchive;

class ZipUtility
{

    /**
     * @param string $archiveFileName
     * @param array  $fileNames
     * @param array  $content
     */
    public function createArchive(
        string $archiveFileName,
        array $fileNames,
        array $content
    ) {
        if (count($fileNames) != count($content)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The count of file names "%d" must be equal to count of content "%d"',
                    count($fileNames),
                    count($content)
                )
            );
        }

        $zip = new ZipArchive();
        $zip->open($archiveFileName, ZipArchive::CREATE);

        $index = 0;
        foreach ($fileNames as $fileName) {
            $zip->addFromString($fileName, $content[$index]);
            $index++;
        }

        $zip->close();
    }

}

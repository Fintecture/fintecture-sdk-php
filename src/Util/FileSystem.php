<?php

namespace Fintecture\Util;

class FileSystem
{
    /**
     * Glob that is safe with streams (vfs for example)
     * See https://github.com/bovigo/vfsStream/issues/2
     *
     * @param string $directory
     * @param string $filePattern
     *
     * @return array
     */
    public static function streamSafeGlob($directory, $filePattern)
    {
        $files = scandir($directory);
        if (!$files) {
            return [];
        }

        $found = [];
        foreach ($files as $filename) {
            if (in_array($filename, ['.', '..'])) {
                continue;
            }

            if (fnmatch($filePattern, $filename)) {
                $found[] = "{$directory}{$filename}";
            }
        }

        return $found;
    }
}

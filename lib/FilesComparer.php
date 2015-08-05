<?php
/**
 * Synchronizer Library
 * Copyright (C) 2014 IceFlame.net
 *
 * Permission to use, copy, modify, and/or distribute this software for
 * any purpose with or without fee is hereby granted, provided that the
 * above copyright notice and this permission notice appear in all copies.
 *
 * @package  FlameCore\Synchronizer
 * @version  0.1-dev
 * @link     http://www.flamecore.org
 * @license  http://opensource.org/licenses/ISC ISC License
 */

namespace FlameCore\Synchronizer\Files;

use FlameCore\Synchronizer\Files\Source\FilesSourceInterface;
use FlameCore\Synchronizer\Files\Target\FilesTargetInterface;

/**
 * The FilesComparer class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class FilesComparer
{
    /**
     * @var array
     */
    protected $outdatedFiles = array();

    /**
     * @var array
     */
    protected $missingDirs = array();

    /**
     * @var array
     */
    protected $missingFiles = array();

    /**
     * @var array
     */
    protected $obsoleteDirs = array();

    /**
     * @var array
     */
    protected $obsoleteFiles = array();

    /**
     * @param \FlameCore\Synchronizer\Files\Source\FilesSourceInterface $source
     * @param \FlameCore\Synchronizer\Files\Target\FilesTargetInterface $target
     * @param array|bool $exclude
     */
    public function __construct(FilesSourceInterface $source, FilesTargetInterface $target, $exclude = false)
    {
        $sourceFiles = $source->getFilesList($exclude);
        $targetFiles = $target->getFilesList();

        ksort($sourceFiles);
        ksort($targetFiles);

        foreach ($sourceFiles as $dir => $files) {
            if (!isset($targetFiles[$dir])) {
                $this->missingDirs[] = $dir;
            }

            foreach ($files as $file => $pathname) {
                if (isset($targetFiles[$dir][$file])) {
                    if ($source->getFileHash($pathname) != $target->getFileHash($pathname)) {
                        $this->outdatedFiles[] = $pathname;
                    }
                } else {
                    $this->missingFiles[] = $pathname;
                }
            }
        }

        foreach ($targetFiles as $dir => $files) {
            foreach ($files as $file => $pathname) {
                if (!isset($sourceFiles[$dir][$file])) {
                    $this->obsoleteFiles[] = $pathname;
                }
            }

            if (!isset($sourceFiles[$dir])) {
                $this->obsoleteDirs[] = $dir;
            }
        }
    }

    /**
     * @return array
     */
    public function getOutdatedFiles()
    {
        return $this->outdatedFiles;
    }

    /**
     * @return array
     */
    public function getMissingDirs()
    {
        return $this->missingDirs;
    }

    /**
     * @return array
     */
    public function getMissingFiles()
    {
        return $this->missingFiles;
    }

    /**
     * @return array
     */
    public function getObsoleteDirs()
    {
        return $this->obsoleteDirs;
    }

    /**
     * @return array
     */
    public function getObsoleteFiles()
    {
        return $this->obsoleteFiles;
    }
}

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
     * @var \FlameCore\Synchronizer\Files\Source\FilesSourceInterface
     */
    protected $source;

    /**
     * @var \FlameCore\Synchronizer\Files\Target\FilesTargetInterface
     */
    protected $target;

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
        $this->source = $source;
        $this->target = $target;

        $sourceFiles = $source->getFilesList($exclude);
        $targetFiles = $target->getFilesList();

        ksort($sourceFiles);
        ksort($targetFiles);

        $this->scan($sourceFiles, $targetFiles);
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

    /**
     * @param array $sourceFiles
     * @param array $targetFiles
     */
    protected function scan(array $sourceFiles, array $targetFiles)
    {
        foreach ($sourceFiles as $dir => $files) {
            if (!isset($targetFiles[$dir])) {
                $this->missingDirs[] = $dir;
            }

            foreach ($files as $file => $pathname) {
                if (isset($targetFiles[$dir][$file])) {
                    if ($this->compare($pathname)) {
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
     * @param string $file
     * @return bool
     */
    protected function compare($file)
    {
        return $this->source->getFileHash($file) !== $this->target->getFileHash($file);
    }
}

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

use FlameCore\Synchronizer\AbstractSynchronizer;
use FlameCore\Synchronizer\SynchronizerSourceInterface;
use FlameCore\Synchronizer\SynchronizerTargetInterface;
use FlameCore\Synchronizer\Files\Location\FilesSourceInterface;
use FlameCore\Synchronizer\Files\Location\FilesTargetInterface;

/**
 * The FilesSynchronizer class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class FilesSynchronizer extends AbstractSynchronizer
{
    /**
     * @var \FlameCore\Synchronizer\Files\Location\FilesSourceInterface
     */
    protected $source;

    /**
     * @var \FlameCore\Synchronizer\Files\Location\FilesTargetInterface
     */
    protected $target;

    /**
     * @var int
     */
    protected $fails = 0;

    /**
     * {@inheritdoc}
     */
    public function synchronize($preserve = true)
    {
        if ($this->observer) {
            $this->observer->notify('sync.start');
        }

        $diff = new FilesComparer($this->source, $this->target, $this->excludes);

        $this->updateOutdated($diff);
        $this->addMissing($diff);

        if (!$preserve) {
            $this->removeObsolete($diff);
        }

        if ($this->observer) {
            $this->observer->notify('sync.finish', ['fails' => $this->fails]);
        }

        return $this->fails == 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsSource(SynchronizerSourceInterface $source)
    {
        return $source instanceof FilesSourceInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTarget(SynchronizerTargetInterface $target)
    {
        return $target instanceof FilesTargetInterface;
    }

    /**
     * @param \FlameCore\Synchronizer\Files\FilesComparer $diff
     */
    protected function updateOutdated(FilesComparer $diff)
    {
        $files = $diff->getOutdatedFiles();

        if ($this->observer) {
            $this->observer->notify('sync.task.start', ['task' => 'Updating outdated files', 'total' => count($files)]);
        }

        foreach ($files as $file) {
            $content = $this->source->get($file);
            if ($content === false || !$this->target->put($file, $content, 0777)) {
                $this->fails++;
            }

            if ($this->observer) {
                $this->observer->notify('sync.task.status');
            }
        }

        if ($this->observer) {
            $this->observer->notify('sync.task.finish');
        }
    }

    /**
     * @param \FlameCore\Synchronizer\Files\FilesComparer $diff
     */
    protected function addMissing(FilesComparer $diff)
    {
        $files = $diff->getMissingFiles();
        $directories = $diff->getMissingDirs();

        if ($this->observer) {
            $this->observer->notify('sync.task.start', ['task' => 'Adding missing files', 'total' => count($files)]);
        }

        foreach ($directories as $directory) {
            if (!$this->target->createDir($directory)) {
                $this->fails++;
            }
        }

        foreach ($files as $file) {
            $content = $this->source->get($file);
            if ($content === false || !$this->target->put($file, $content, 0777)) {
                $this->fails++;
            }

            if ($this->observer) {
                $this->observer->notify('sync.task.status');
            }
        }

        if ($this->observer) {
            $this->observer->notify('sync.task.finish');
        }
    }

    /**
     * @param \FlameCore\Synchronizer\Files\FilesComparer $diff
     */
    protected function removeObsolete(FilesComparer $diff)
    {
        $files = $diff->getObsoleteFiles();
        $directories = $diff->getObsoleteDirs();

        if ($this->observer) {
            $this->observer->notify('sync.task.start', ['task' => 'Removing obsolete files', 'total' => count($files)]);
        }

        foreach ($files as $file) {
            if (!$this->target->remove($file)) {
                $this->fails++;
            }

            if ($this->observer) {
                $this->observer->notify('sync.task.status');
            }
        }

        foreach ($directories as $directory) {
            if (!$this->target->removeDir($directory)) {
                $this->fails++;
            }
        }

        if ($this->observer) {
            $this->observer->notify('sync.task.finish');
        }
    }
}

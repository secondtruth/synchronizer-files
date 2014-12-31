<?php
/**
 * Synchronizer Library
 * Copyright (C) 2014 IceFlame.net
 *
 * Permission to use, copy, modify, and/or distribute this software for
 * any purpose with or without fee is hereby granted, provided that the
 * above copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE
 * FOR ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY
 * DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER
 * IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING
 * OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 *
 * @package  FlameCore\Synchronizer
 * @version  0.1-dev
 * @link     http://www.flamecore.org
 * @license  ISC License <http://opensource.org/licenses/ISC>
 */

namespace FlameCore\Synchronizer\Files;

use FlameCore\Synchronizer\AbstractSynchronizer;
use FlameCore\Synchronizer\SynchronizerSourceInterface;
use FlameCore\Synchronizer\SynchronizerTargetInterface;

/**
 * The FilesSynchronizer class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class FilesSynchronizer extends AbstractSynchronizer
{
    /**
     * {@inheritdoc}
     */
    public function synchronize($preserve = true)
    {
        $diff = new FilesComparer($this->source, $this->target, $this->excludes);

        $this->updateOutdated($diff);
        $this->addMissing($diff);

        if (!$preserve) {
            $this->removeObsolete($diff);
        }
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

        foreach ($files as $file) {
            $this->target->put($file, $this->source->get($file), $this->source->getFileMode($file));
        }
    }

    /**
     * @param \FlameCore\Synchronizer\Files\FilesComparer $diff
     */
    protected function addMissing(FilesComparer $diff)
    {
        $files = $diff->getMissingFiles();
        $directories = $diff->getMissingDirs();

        foreach ($directories as $directory) {
            $this->target->createDir($directory, $this->source->getFileMode($directory));
        }

        foreach ($files as $file) {
            $this->target->put($file, $this->source->get($file), $this->source->getFileMode($file));
        }
    }

    /**
     * @param \FlameCore\Synchronizer\Files\FilesComparer $diff
     */
    protected function removeObsolete(FilesComparer $diff)
    {
        $files = $diff->getObsoleteFiles();
        $directories = $diff->getObsoleteDirs();

        foreach ($files as $file) {
            $this->target->remove($file);
        }

        foreach ($directories as $directory) {
            $this->target->removeDir($directory);
        }
    }
}

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

namespace FlameCore\Synchronizer\Files\Target;

use FlameCore\Synchronizer\Files\Location\LocalFilesLocation;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The LocalFilesTarget class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class LocalFilesTarget extends LocalFilesLocation implements FilesTargetInterface
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @param array $settings
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(array $settings, Filesystem $filesystem = null)
    {
        parent::__construct($settings);

        $this->filesystem = $filesystem ?: new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function get($file)
    {
        return file_get_contents($this->getRealPathName($file));
    }

    /**
     * {@inheritdoc}
     */
    public function put($file, $content, $mode)
    {
        $filename = $this->getRealPathName($file);

        try {
            $this->filesystem->dumpFile($filename, $content, $mode);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function chmod($file, $mode)
    {
        try {
            $this->filesystem->chmod($this->getRealPathName($file), $mode);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($file)
    {
        try {
            $this->filesystem->remove($this->getRealPathName($file));

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($name, $mode = 0777)
    {
        try {
            $this->filesystem->mkdir($this->getRealPathName($name), $mode, true);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeDir($name)
    {
        try {
            $this->filesystem->remove($this->getRealPathName($name));

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

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
    protected $fs;

    /**
     * @param array $settings
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(array $settings, Filesystem $filesystem = null)
    {
        parent::__construct($settings);

        $this->fs = $filesystem ?: new Filesystem();
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

        return $this->fs->dumpFile($filename, $content, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function chmod($file, $mode)
    {
        return $this->fs->chmod($this->getRealPathName($file), $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($file)
    {
        return $this->fs->remove($this->getRealPathName($file));
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($name, $mode = 0777)
    {
        return $this->fs->mkdir($this->getRealPathName($name), $mode, true);
    }

    /**
     * {@inheritdoc}
     */
    public function removeDir($name)
    {
        return $this->fs->remove($this->getRealPathName($name));
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesList($exclude = false)
    {
        $fileslist = array();

        $iterator = new \RecursiveDirectoryIterator($this->path,
            \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);

        if ((is_string($exclude) || is_array($exclude)) && !empty($exclude)) {
            $iterator = new \RecursiveCallbackFilterIterator($iterator, function ($current) use ($exclude) {
                if ($current->isDir()) {
                    return true;
                }

                $subpath = substr($current->getPathName(), strlen($this->path) + 1);

                foreach ((array) $exclude as $pattern) {
                    if ($pattern[0] == '!' ? !fnmatch(substr($pattern, 1), $subpath) : fnmatch($pattern, $subpath)) {
                        return false;
                    }
                }

                return true;
            });
        }

        $iterator = new \RecursiveIteratorIterator($iterator);
        foreach ($iterator as $file) {
            $pathname = substr($file->getPathName(), strlen($this->path));
            $filename = $file->getBasename();
            $dirname = dirname($pathname);

            $fileslist[$dirname][$filename] = substr($pathname, 1);
        }

        return $fileslist;
    }

    /**
     * {@inheritdoc}
     */
    public function getRealPathName($file)
    {
        return $this->path . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileMode($file)
    {
        $fileperms = fileperms($this->getRealPathName($file));

        return substr(decoct($fileperms), 2);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileHash($file)
    {
        $filename = $this->getRealPathName($file);

        return is_readable($filename) ? hash_file('crc32b', $filename) : false;
    }
}

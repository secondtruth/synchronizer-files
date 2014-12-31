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

/**
 * The LocalFilesTarget class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class LocalFilesTarget extends AbstractFilesTarget
{
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

        if (file_put_contents($filename, $content)) {
            @chmod($filename, $mode);
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function chmod($file, $mode)
    {
        return chmod($this->getRealPathName($file), $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($file)
    {
        return unlink($this->getRealPathName($file));
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($name, $mode = 0777)
    {
        return mkdir($this->getRealPathName($name), $mode, true);
    }

    /**
     * {@inheritdoc}
     */
    public function removeDir($name)
    {
        return rmdir($this->getRealPathName($name));
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesList($exclude = false)
    {
        $fileslist = array();

        $iterator = new \RecursiveDirectoryIterator($this->path, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);

        if ((is_string($exclude) || is_array($exclude)) && !empty($exclude)) {
            $iterator = new \RecursiveCallbackFilterIterator($iterator, function ($current, $key, $iterator) use ($exclude) {
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
            $dirname = $file->getPath();
            $filename = $file->getBasename();

            $fileslist[$dirname][$filename] = $dirname.'/'.$filename;
        }

        return $fileslist;
    }

    /**
     * {@inheritdoc}
     */
    public function getRealPathName($file)
    {
        return realpath($this->path . DIRECTORY_SEPARATOR . $file);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileMode($file)
    {
        $fileperms = fileperms($this->getRealPathName($file));

        return (int) substr(decoct($fileperms), 2);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileHash($file)
    {
        return hash_file('crc32b', $this->getRealPathName($file));
    }

    /**
     * {@inheritdoc}
     */
    protected function discoverTarget(array $settings)
    {
        if (!isset($settings['dir']) || !is_string($settings['dir'])) {
            throw new \InvalidArgumentException(sprintf('The %s does not define "dir" setting.', get_class($this)));
        }

        return !$this->isAbsolutePath($settings['dir']) ? realpath(getcwd() . DIRECTORY_SEPARATOR . $settings['dir']) : $settings['dir'];
    }
}

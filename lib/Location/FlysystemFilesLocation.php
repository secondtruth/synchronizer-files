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

namespace FlameCore\Synchronizer\Files\Location;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\Ftp;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListFiles;
use League\Flysystem\Util;

/**
 * The FlysystemFilesLocation class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class FlysystemFilesLocation implements  FilesLocationInterface
{
    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;

    /**
     * @param array $settings
     * @param \League\Flysystem\AdapterInterface $adapter
     */
    public function __construct(array $settings, AdapterInterface $adapter = null)
    {
		$adapter = $adapter ?: $this->createAdapter($settings);
		
		$filesystem = new Filesystem($adapter, $settings);
        $filesystem->addPlugin(new ListFiles());

        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesList($exclude = false)
    {
        $fileslist = array();

        $files = $this->filesystem->listFiles('', true);
        foreach ($files as $file) {
            $filename = basename($file['path']);
            $dirname = dirname($file['path']);

            $fileslist[$dirname][$filename] = $file['path'];
        }

        return $fileslist;
    }

    /**
     * {@inheritdoc}
     */
    public function getRealPathName($file)
    {
        return Util::normalizePath($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileMode($file)
    {
        $visibility = $this->filesystem->getVisibility($file);

        if ($visibility) {
            return $visibility == AdapterInterface::VISIBILITY_PRIVATE ? 0700 : 0744;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFileHash($file)
    {
        $content = $this->filesystem->read($file);

        return hash('crc32b', $content);
    }

    /**
     * @param array $settings
     * @return bool
     */
    protected function createAdapter(array $settings)
    {
        $type = isset($settings['type']) ? (string) $settings['type'] : 'local';

        if ($type == 'local') {
            if (!isset($settings['dir']) || !is_string($settings['dir'])) {
                throw new \InvalidArgumentException(sprintf('The %s does not define "dir" setting.', get_class($this)));
            }

            return new Local($settings['dir']);
        } elseif ($type == 'ftp') {
            return new Ftp($settings);
        } else {
            throw new \DomainException(sprintf('The Flysystem adapter "%s" does not exist.', $type));
        }
    }
}

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
class FlysystemFilesLocation implements FilesSourceInterface, FilesTargetInterface
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
    public function get($file)
    {
        return $this->filesystem->read($file);
    }

    /**
     * {@inheritdoc}
     */
    public function put($file, $content, $mode)
    {
        if ($this->filesystem->put($file, $content)) {
            $this->chmod($file, $mode);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function chmod($file, $mode)
    {
        $visibility = $mode & 0044 ? AdapterInterface::VISIBILITY_PUBLIC : AdapterInterface::VISIBILITY_PRIVATE;

        return $this->filesystem->setVisibility($file, $visibility);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($file)
    {
        return $this->filesystem->delete($file);
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($name, $mode = 0777)
    {
        if ($this->filesystem->createDir($name)) {
            $this->chmod($name, $mode);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function removeDir($name)
    {
        return $this->filesystem->deleteDir($name);
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
     * @return AdapterInterface
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

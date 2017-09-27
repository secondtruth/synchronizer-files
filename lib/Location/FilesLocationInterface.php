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

/**
 * The FilesLocation interface
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
interface FilesLocationInterface
{
    /**
     * @param string $file
     * @return string|bool
     */
    public function get($file);

    /**
     * @param array|bool $exclude
     * @return array
     */
    public function getFilesList($exclude = false);

    /**
     * @param string $file
     * @return string
     */
    public function getRealPathName($file);

    /**
     * @param string $file
     * @return int|false
     */
    public function getFileMode($file);

    /**
     * @param string $file
     * @return string|false
     */
    public function getFileHash($file);
}

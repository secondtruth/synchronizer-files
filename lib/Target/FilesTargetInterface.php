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

namespace FlameCore\Synchronizer\Files\Target;

use FlameCore\Synchronizer\Files\Location\FilesLocationInterface;
use FlameCore\Synchronizer\SynchronizerTargetInterface;

/**
 * The FilesTarget interface
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
interface FilesTargetInterface extends FilesLocationInterface, SynchronizerTargetInterface
{
    /**
     * @param string $file
     * @return string|bool
     */
    public function get($file);

    /**
     * @param string $file
     * @param string $content
     * @param int $mode
     * @return bool
     */
    public function put($file, $content, $mode);

    /**
     * @param string $file
     * @param int $mode
     * @return bool
     */
    public function chmod($file, $mode);

    /**
     * @param string $file
     * @return bool
     */
    public function remove($file);

    /**
     * @param string $name
     * @param int $mode
     * @return bool
     */
    public function createDir($name, $mode = 0777);

    /**
     * @param string $name
     * @return bool
     */
    public function removeDir($name);
}

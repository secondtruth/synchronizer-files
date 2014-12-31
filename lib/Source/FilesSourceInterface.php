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

namespace FlameCore\Synchronizer\Files\Source;

use FlameCore\Synchronizer\SynchronizerSourceInterface;

/**
 * The FilesSource interface
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
interface FilesSourceInterface extends SynchronizerSourceInterface
{
    /**
     * @param string $file
     * @return string
     */
    public function get($file);

    /**
     * @return string
     */
    public function getFilesPath();

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
     * @return int
     */
    public function getFileMode($file);

    /**
     * @param string $file
     * @return string
     */
    public function getFileHash($file);
}

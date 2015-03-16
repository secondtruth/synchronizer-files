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

/**
 * The FilesLocation class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class LocalFilesLocation implements FilesLocationInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $settings)
    {
        if (!isset($settings['dir']) || !is_string($settings['dir'])) {
            throw new \InvalidArgumentException(sprintf('The %s does not define "dir" setting.', get_class($this)));
        }

        $this->path = !$this->isAbsolutePath($settings['dir']) ? realpath(getcwd() . DIRECTORY_SEPARATOR . $settings['dir']) : $settings['dir'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function isAbsolutePath($path)
    {
        return $path[0] === DIRECTORY_SEPARATOR || preg_match('#^(?:/|\\\\|[A-Za-z]:\\\\|[A-Za-z]:/)#', $path);
    }
}

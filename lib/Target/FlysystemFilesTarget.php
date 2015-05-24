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

use FlameCore\Synchronizer\Files\Location\FlysystemFilesLocation;
use League\Flysystem\AdapterInterface;

/**
 * The FlysystemFilesTarget class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class FlysystemFilesTarget extends FlysystemFilesLocation implements FilesTargetInterface
{
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
}

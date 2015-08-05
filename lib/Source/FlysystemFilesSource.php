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

namespace FlameCore\Synchronizer\Files\Source;

use FlameCore\Synchronizer\Files\Location\FlysystemFilesLocation;

/**
 * The FlysystemFilesSource class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class FlysystemFilesSource extends FlysystemFilesLocation implements FilesSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($file)
    {
        return $this->filesystem->read($file);
    }
}

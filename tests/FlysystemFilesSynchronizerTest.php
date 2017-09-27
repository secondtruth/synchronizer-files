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

namespace FlameCore\Synchronizer\Files\Tests;

use FlameCore\Synchronizer\Files\FilesSynchronizer;
use FlameCore\Synchronizer\Files\Location\FlysystemFilesLocation;

/**
 * Test class for Flysystem FilesSynchronizer.
 */
class FlysystemFilesSynchronizerTest extends FilesSynchronizerTestCase
{
    /**
     * @var \FlameCore\Synchronizer\Files\FilesSynchronizer
     */
    private $synchronizer;

    public function setUp()
    {
        parent::setUp();

        $source = new FlysystemFilesLocation(['dir' => $this->sourcePath]);
        $target = new FlysystemFilesLocation(['dir' => $this->targetPath]);

        $this->synchronizer = new FilesSynchronizer($source, $target);
    }

    public function testSynchronizer()
    {
        $this->synchronizer->synchronize();

        $this->assertNewFileCreated();
        $this->assertFileModified();
    }

    public function testSynchronizerPreserveDisabled()
    {
        $this->synchronizer->synchronize(false);

        $this->assertObsoleteFileDeleted();
    }
}

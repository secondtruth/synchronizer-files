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

use FlameCore\Synchronizer\Files\FilesSynchronizerFactory;

/**
 * Test class for FilesSynchronizerFactory.
 */
class FilesSynchronizerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \FlameCore\Synchronizer\Files\FilesSynchronizerFactory
     */
    private $factory;

    public function setUp()
    {
        $factory = new FilesSynchronizerFactory();
        $factory->registerSource('local', 'FlameCore\Synchronizer\Files\Source\LocalFilesSource');
        $factory->registerTarget('local', 'FlameCore\Synchronizer\Files\Target\LocalFilesTarget');

        $this->factory = $factory;
    }

    public function testFactoryCreatesSynchronizer()
    {
        $actual = $this->factory->create(['dir' => '.'], ['dir' => '.']);

        $this->assertInstanceOf('FlameCore\Synchronizer\Files\FilesSynchronizer', $actual);
    }

    public function testFactoryCreatesSource()
    {
        $actual = $this->factory->createSource(['dir' => '.']);

        $this->assertInstanceOf('FlameCore\Synchronizer\Files\Source\LocalFilesSource', $actual);
    }

    public function testFactoryCreatesTarget()
    {
        $actual = $this->factory->createTarget(['dir' => '.']);

        $this->assertInstanceOf('FlameCore\Synchronizer\Files\Target\LocalFilesTarget', $actual);
    }
}

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
    const TEST_SYNCHRONIZER_CLASS = 'FlameCore\Synchronizer\Files\FilesSynchronizer';
    const TEST_LOCATION_CLASS = 'FlameCore\Synchronizer\Files\Location\LocalFilesLocation';

    /**
     * @var \FlameCore\Synchronizer\Files\FilesSynchronizerFactory
     */
    private $factory;

    public function setUp()
    {
        $factory = new FilesSynchronizerFactory();
        $factory->registerSource('local', self::TEST_LOCATION_CLASS);
        $factory->registerTarget('local', self::TEST_LOCATION_CLASS);
        $factory->registerSource('foo', self::TEST_LOCATION_CLASS);
        $factory->registerTarget('foo', self::TEST_LOCATION_CLASS);

        $this->factory = $factory;
    }

    public function testFactoryCreatesSynchronizer()
    {
        $actual = $this->factory->create(['dir' => '.'], ['dir' => '.']);

        $this->assertInstanceOf(self::TEST_SYNCHRONIZER_CLASS, $actual);
    }

    public function testFactoryCreatesDefaultSource()
    {
        $actual = $this->factory->createSource(['dir' => '.']);

        $this->assertInstanceOf(self::TEST_LOCATION_CLASS, $actual);
    }

    public function testFactoryCreatesDefaultTarget()
    {
        $actual = $this->factory->createTarget(['dir' => '.']);

        $this->assertInstanceOf(self::TEST_LOCATION_CLASS, $actual);
    }

    public function testFactoryCreatesAlternativeSource()
    {
        $actual = $this->factory->createSource(['type' => 'foo', 'dir' => '.']);

        $this->assertInstanceOf(self::TEST_LOCATION_CLASS, $actual);
    }

    public function testFactoryCreatesAlternativeTarget()
    {
        $actual = $this->factory->createTarget(['type' => 'foo', 'dir' => '.']);

        $this->assertInstanceOf(self::TEST_LOCATION_CLASS, $actual);
    }
}

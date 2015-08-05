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

abstract class FilesSynchronizerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $workspace = null;

    /**
     * @var string
     */
    protected $sourcePath;

    /**
     * @var string
     */
    protected $targetPath;

    /**
     * @var int
     */
    private $umask;

    public function setUp()
    {
        $this->umask = umask(0);
        $this->workspace = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.time().mt_rand(0, 1000);
        mkdir($this->workspace, 0777, true);
        $this->workspace = realpath($this->workspace);

        $this->fillWorkspace();
    }

    public function tearDown()
    {
        $this->clean($this->workspace);
        umask($this->umask);
    }

    protected function assertNewFileCreated()
    {
        $file = $this->targetPath.DIRECTORY_SEPARATOR.'new.txt';

        $this->assertFileExists($file);
        $this->assertEquals('CONTENT', file_get_contents($file));
    }

    protected function assertFileModified()
    {
        $file = $this->targetPath.DIRECTORY_SEPARATOR.'modified.txt';

        $this->assertFileExists($file);
        $this->assertEquals('MODIFIED CONTENT', file_get_contents($file));
    }

    protected function assertObsoleteFileDeleted()
    {
        $file = $this->targetPath.DIRECTORY_SEPARATOR.'obsolete.txt';

        $this->assertFileNotExists($file);
    }

    /**
     * @param int    $expected expected file permissions as three digits (i.e. 755)
     * @param string $filePath
     */
    protected function assertFilePermissions($expected, $filePath)
    {
        $actual = (int) substr(sprintf('%o', fileperms($filePath)), -3);
        $this->assertEquals(
            $expected, $actual,
            sprintf('File permissions for %s must be %s. Actual %s', $filePath, $expected, $actual)
        );
    }

    protected function fillWorkspace()
    {
        $this->sourcePath = $this->fillWorkspaceWithSource();
        $this->targetPath = $this->fillWorkspaceWithTarget();
    }

    /**
     * @param bool $withFiles
     * @return string
     */
    protected function fillWorkspaceWithSource($withFiles = true)
    {
        $sourcePath = $this->workspace.DIRECTORY_SEPARATOR.'source';
        mkdir($sourcePath);

        if ($withFiles) {
            file_put_contents($sourcePath.DIRECTORY_SEPARATOR.'new.txt', 'CONTENT');
            file_put_contents($sourcePath.DIRECTORY_SEPARATOR.'modified.txt', 'MODIFIED CONTENT');
        }

        return $sourcePath;
    }

    /**
     * @param bool $withFiles
     * @return string
     */
    protected function fillWorkspaceWithTarget($withFiles = true)
    {
        $targetPath = $this->workspace.DIRECTORY_SEPARATOR.'target';
        mkdir($targetPath);

        if ($withFiles) {
            file_put_contents($targetPath.DIRECTORY_SEPARATOR.'modified.txt', 'OLD CONTENT');
            file_put_contents($targetPath.DIRECTORY_SEPARATOR.'obsolete.txt', 'CONTENT');
        }

        return $targetPath;
    }

    /**
     * @param string $file
     */
    protected function clean($file)
    {
        if (is_dir($file) && !is_link($file)) {
            $dir = new \FilesystemIterator($file);
            foreach ($dir as $childFile) {
                $this->clean($childFile);
            }

            rmdir($file);
        } else {
            unlink($file);
        }
    }
}

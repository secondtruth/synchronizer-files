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

namespace FlameCore\Synchronizer\Files\Tests;

abstract class FilesSynchronizerTestCase extends \PHPUnit_Framework_TestCase
{
    private $umask;

    /**
     * @var string $workspace
     */
    protected $workspace = null;

    public function setUp()
    {
        $this->umask = umask(0);
        $this->workspace = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.time().rand(0, 1000);
        mkdir($this->workspace, 0777, true);
        $this->workspace = realpath($this->workspace);
    }

    public function tearDown()
    {
        $this->clean($this->workspace);
        umask($this->umask);
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

    /**
     * @param int    $expectedFilePerms expected file permissions as three digits (i.e. 755)
     * @param string $filePath
     */
    protected function assertFilePermissions($expectedFilePerms, $filePath)
    {
        $actualFilePerms = (int) substr(sprintf('%o', fileperms($filePath)), -3);
        $this->assertEquals(
            $expectedFilePerms,
            $actualFilePerms,
            sprintf('File permissions for %s must be %s. Actual %s', $filePath, $expectedFilePerms, $actualFilePerms)
        );
    }
}

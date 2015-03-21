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

use FlameCore\Synchronizer\Files\FilesSynchronizer;
use FlameCore\Synchronizer\Files\Source\LocalFilesSource;
use FlameCore\Synchronizer\Files\Target\LocalFilesTarget;

/**
 * Test class for local FilesSynchronizer.
 */
class LocalFilesSynchronizerTest extends FilesSynchronizerTestCase
{
    public function testSynchronizerSucceeds()
    {
        $sourcePath = $this->fillWorkspaceWithSource();
        $targetPath = $this->fillWorkspaceWithTarget();

        $source = new LocalFilesSource(['dir' => $sourcePath]);
        $target = new LocalFilesTarget(['dir' => $targetPath]);

        $synchronizer = new FilesSynchronizer($source, $target);
        $synchronizer->synchronize();

        $file1 = $targetPath.DIRECTORY_SEPARATOR.'new.txt';
        $file2 = $targetPath.DIRECTORY_SEPARATOR.'modified.txt';

        $this->assertFileExists($file1);
        $this->assertEquals('CONTENT', file_get_contents($file1));

        $this->assertFileExists($file2);
        $this->assertEquals('MODIFIED CONTENT', file_get_contents($file2));
    }
}

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

namespace FlameCore\Synchronizer\Files;

use FlameCore\Synchronizer\SynchronizerFactoryInterface;

/**
 * The FilesSynchronizerFactory class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class FilesSynchronizerFactory implements SynchronizerFactoryInterface
{
    protected static $sources = [
        'local' => 'LocalFilesSource'
    ];
    
    protected static $targets = [
        'local' => 'LocalFilesTarget'
    ];
    
    /**
     * {@inheritdoc}
     */
    public function create(array $sourceSettings, array $targetSettings)
    {
        $source = $this->createSource($sourceSettings);
        $target = $this->createTarget($targetSettings);

        $object = new FilesSynchronizer();
        $object->setSource($source);
        $object->setTarget($target);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function createSource(array $settings)
    {
        if (isset($settings['type'])) {
            $type = (string) $settings['type'];
            
            if (!isset(self::$sources[$type])) {
                throw new \DomainException(sprintf('The source type "%s" is invalid.', $type));
            }
        } else {
            $type = 'local';
        }

        $class = sprintf('%s\Source\%s', __NAMESPACE__, self::$sources[$type]);

        return new $class($settings);
    }

    /**
     * {@inheritdoc}
     */
    public function createTarget(array $settings)
    {
        if (isset($settings['type'])) {
            $type = (string) $settings['type'];
            
            if (!isset(self::$targets[$type])) {
                throw new \DomainException(sprintf('The target type "%s" is invalid.', $type));
            }
        } else {
            $type = 'local';
        }

        $class = sprintf('%s\Target\%s', __NAMESPACE__, self::$targets[$type]);

        return new $class($settings);
    }
}

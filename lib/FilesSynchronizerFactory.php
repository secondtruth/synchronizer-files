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

use FlameCore\Synchronizer\Files\Location\FilesLocationInterface;
use FlameCore\Synchronizer\SynchronizerFactoryInterface;

/**
 * The FilesSynchronizerFactory class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class FilesSynchronizerFactory implements SynchronizerFactoryInterface
{
    /**
     * @var array
     */
    protected $sources = array();

    /**
     * @var array
     */
    protected $targets = array();

    /**
     * {@inheritdoc}
     */
    public function create(array $sourceSettings, array $targetSettings)
    {
        $source = $this->createSource($sourceSettings);
        $target = $this->createTarget($targetSettings);

        return new FilesSynchronizer($source, $target);
    }

    /**
     * {@inheritdoc}
     */
    public function createSource(array $settings)
    {
        $type = isset($settings['type']) ? $this->normalize($settings['type']) : 'local';

        if (!isset($this->sources[$type])) {
            throw new \DomainException(sprintf('The source type "%s" does not exist.', $type));
        }

        $class = $this->sources[$type]['class'];
        $initializer = $this->sources[$type]['initializer'];

        return $this->createObject($class, $initializer, $settings);
    }

    /**
     * {@inheritdoc}
     */
    public function createTarget(array $settings)
    {
        $type = isset($settings['type']) ? $this->normalize($settings['type']) : 'local';

        if (!isset($this->targets[$type])) {
            throw new \DomainException(sprintf('The target type "%s" does not exist.', $type));
        }

        $class = $this->targets[$type]['class'];
        $initializer = $this->targets[$type]['initializer'];

        return $this->createObject($class, $initializer, $settings);
    }

    /**
     * @param string $type
     * @param string $class
     * @param callable $initializer
     */
    public function registerSource($type, $class, callable $initializer = null)
    {
        $type = $this->normalize($type);

        if (isset($this->sources[$type])) {
            throw new \LogicException(sprintf('The source type "%s" already exists.', $type));
        }

        if (!class_exists($class)) {
            throw new \DomainException('The given class does not exist.');
        }

        $this->sources[$type] = array(
            'class'       => $class,
            'initializer' => $initializer
        );
    }

    /**
     * @param string $type
     * @param string $class
     * @param callable $initializer
     */
    public function registerTarget($type, $class, callable $initializer = null)
    {
        $type = $this->normalize($type);

        if (isset($this->targets[$type])) {
            throw new \LogicException(sprintf('The target type "%s" already exists.', $type));
        }

        if (!class_exists($class)) {
            throw new \DomainException('The given class does not exist.');
        }

        $this->targets[$type] = array(
            'class'       => $class,
            'initializer' => $initializer
        );
    }

    /**
     * @param string $name
     * @return string
     */
    protected function normalize($name)
    {
        $name = (string) $name;

        if ($name === '') {
            throw new \InvalidArgumentException('The type name must not be empty.');
        }

        return $name;
    }

    /**
     * @param string $class
     * @param callable $initializer
     * @param array $settings
     * @return object
     * @throws \UnexpectedValueException
     */
    protected function createObject($class, callable $initializer = null, array $settings = [])
    {
        if ($initializer) {
            $object = $initializer($class, $settings);

            if (!$object instanceof FilesLocationInterface) {
                throw new \UnexpectedValueException(sprintf('The initializer for class %s does not return a valid object.', $class));
            }

            return $object;
        } else {
            return new $class($settings);
        }
    }
}

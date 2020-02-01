<?php

/**
 * Spiral Framework. Code Style
 *
 * @license MIT
 * @author  Aleksandr Novikov (alexndr-novikov)
 */
declare(strict_types=1);

namespace Spiral\Tests\CodeStyle;

class CodeStyleCheckTest extends AbstractCodeStyleTest
{
    public function testWrongFormattedClass(): void
    {
        $out = [];
        exec('bin/spiral-cs check ' . $this->getRelativeFilePath(self::NOT_FORMATTED_FILE_NAME), $out);
        $this->assertArrayHasKey(0, $out);
        $this->assertGreaterThan(1, count($out));
        $this->assertNotSame($out[0], 'No codestyle issues');
    }

    public function testWellFormattedClass(): void
    {
        $out = [];
        exec($command = 'bin/spiral-cs check ' . $this->getRelativeFilePath(self::FORMATTED_FILE_NAME), $out);
        $this->assertArrayHasKey(0, $out);
        $this->assertEquals($out[0], 'No codestyle issues');
    }
}

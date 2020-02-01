<?php

/**
 * Spiral Framework. Code Style
 *
 * @license MIT
 * @author  Aleksandr Novikov (alexndr-novikov)
 */
declare(strict_types=1);

namespace Spiral\Tests\CodeStyle;

class CodeStyleFixTest extends AbstractCodeStyleTest
{
    public function testFix(): void
    {
        exec('bin/spiral-cs fix ' . $this->getRelativeFilePath(self::NOT_FORMATTED_FILE_NAME));
        $this->assertFileEquals(
            $this->notFormattedClassFilePath,
            $this->getFixturesFilePath(self::FORMATTED_FILE_NAME)
        );
    }
}

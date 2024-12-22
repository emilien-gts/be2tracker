<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhpUnitTest extends KernelTestCase
{
    public function testPhpUnitIsUp(): void
    {
        /* @phpstan-ignore-next-line */
        $this->assertTrue(true);
    }
}

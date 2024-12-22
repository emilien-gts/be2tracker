<?php

declare(strict_types=1);

namespace App\Story;

use App\Tests\Factory\BankrollFactory;
use Zenstruck\Foundry\Story;

final class AppStory extends Story
{
    public function build(): void
    {
        BankrollFactory::createMany(2);
    }
}

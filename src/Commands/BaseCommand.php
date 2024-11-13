<?php

declare(strict_types=1);

namespace Intervention\Pinboard\Commands;

use Intervention\Pinboard\Application;
use Symfony\Component\Console\Command\Command;

abstract class BaseCommand extends Command
{
    public function app(): Application
    {
        return $this->getApplication();
    }
}

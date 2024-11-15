<?php

declare(strict_types=1);

namespace Intervention\Pinboard\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Intervention\Pinboard\Models\Bookmark;

class StatusCommand extends AbstractBaseCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('status');
        $this->setDescription('Get current status of local database.');
    }

    /**
     * Execution handler
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = sprintf('%d bookmarks in database', Bookmark::count());
        $lastupdate = sprintf('Last update %s', Bookmark::lastUpdatedAt()->diffForHumans());

        $output->writeln("<info>" . $count . "</info>");
        $output->writeln("<comment>" . $lastupdate . "</comment>");

        return self::SUCCESS;
    }
}

<?php

namespace Intervention\Pinboard\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Intervention\Pinboard\Bookmark;

class StatusCommand extends Command
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('status');
        $this->setDescription('Get current status of local database.');
    }

    /**
     * Execution handler
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = sprintf('%d bookmarks in database', Bookmark::count());
        $lastupdate = sprintf('Last update %s', Bookmark::lastUpdatedAt()->diffForHumans());

        $output->writeln("<info>" . $count . "</info>");
        $output->writeln("<comment>" . $lastupdate . "</comment>");

        return 0;
    }
}

<?php

namespace Intervention\Pinboard\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Intervention\Pinboard\Bookmark;

class SearchCommand extends Command
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('search');
        $this->setDescription('Search in local bookmark database');
        $this->addArgument('keyword', InputArgument::OPTIONAL);
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
        if ($keyword = $input->getArgument('keyword')) {
            $bookmarks = Bookmark::search($keyword)->get();

            if ($bookmarks->count()) {
                foreach ($bookmarks as $bookmark) {
                    // TODO
                }
            }
        }

        return 0;
    }
}

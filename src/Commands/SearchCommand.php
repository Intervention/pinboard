<?php

declare(strict_types=1);

namespace Intervention\Pinboard\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Intervention\Pinboard\Models\Bookmark;
use Symfony\Component\Console\Input\ArrayInput;

class SearchCommand extends AbstractBaseCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('search');
        $this->setDescription('Search in local bookmark database');
        $this->addArgument('keyword', InputArgument::REQUIRED);
        $this->addOption('short', 's', null, 'Display only the url in search results.');
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
        // check if database needs to be updated
        if ($this->app()->databaseUpdateNecessary()) {
            $output->writeln("<info>Bringing the database up to date ... </info>");
            $returnCode = $this->app()->doRun(
                new ArrayInput(['command' => 'pull']),
                $output,
            );

            if ($returnCode !== self::SUCCESS) {
                return self::FAILURE;
            }
        }

        // search for bookmarks
        $bookmarks = Bookmark::search($input->getArgument('keyword'))
            ->orderBy('timestamp')
            ->get();

        if ($bookmarks->count() == 0) {
            return self::INVALID;
        }

        foreach ($bookmarks as $bookmark) {
            $bookmark->output($output, $input->getOption('short'));
        }

        return self::SUCCESS;
    }
}

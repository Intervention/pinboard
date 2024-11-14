<?php

declare(strict_types=1);

namespace Intervention\Pinboard\Commands;

use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Intervention\Pinboard\Models\Bookmark;
use Intervention\Pinboard\Models\Tag;
use Symfony\Component\Console\Input\ArrayInput;

class SearchCommand extends BaseCommand
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
        $bookmarks = Bookmark::search($input->getArgument('keyword'))->get();

        if ($bookmarks->count() == 0) {
            return self::INVALID;
        }

        $output->writeln('');

        foreach ($bookmarks as $bookmark) {
            $output->writeln("<info>📌 " . ($bookmark->title ? $bookmark->title : $bookmark->url) . "</info>");
            if ($bookmark->tags->count()) {
                $output->writeln("   " . $this->formatTags($bookmark->tags));
            }
            $output->writeln("   <fg=bright-green;options=bold,underscore>" . $bookmark->url . "</>");
            $output->write(PHP_EOL);
        }

        return self::SUCCESS;
    }

    /**
     * Format given text for output in cli
     *
     * @param Collection $tags
     * @return string
     */
    private function formatTags(Collection $tags): string
    {
        return $tags->map(function (Tag $tag) {
            return "<comment>" . $tag->title . "</comment>";
        })->join(" ");
    }
}

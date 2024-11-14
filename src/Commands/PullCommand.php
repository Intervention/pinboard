<?php

declare(strict_types=1);

namespace Intervention\Pinboard\Commands;

use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Intervention\Pinboard\Models\Bookmark;
use Intervention\Pinboard\Models\Tag;

class PullCommand extends BaseCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('pull');
        $this->setDescription('Pull bookmarks from Pinboard account to local database.');
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
        try {
            $pins = new Collection(
                $this->app()->api()->get_all(null)
            );
        } catch (Exception $e) {
            $output->writeln("<error>Failed to load data from Pinboard. (" . $e->getMessage() . ") </error>");
            return self::FAILURE;
        }

        if ($pins->count() === 0) {
            $output->writeln("<error>No bookmarks found in Pinboard account.</error>");
            return self::SUCCESS;
        }

        // reset database
        $returnCode = $this->app()->doRun(new ArrayInput(['command' => 'init']), $output);
        if ($returnCode !== self::SUCCESS) {
            return self::FAILURE;
        }

        $output->writeln("<info>Syncing " . $pins->count() . " bookmarks ...</info>");
        $bar = new ProgressBar($output, $pins->count());
        $bar->start();

        foreach ($pins as $pin) {
            // save bookmark
            $bookmark = Bookmark::create([
                'title' => $pin->title,
                'url' => $pin->url,
                'timestamp' => $pin->timestamp
            ]);

            // save tags
            foreach ($pin->tags as $tag) {
                if (!empty($tag)) {
                    $bookmark->tags()->save(new Tag([
                        'title' => $tag
                    ]));
                }
            }

            $bar->advance();
        }

        $bar->finish();
        echo PHP_EOL;

        $output->writeln("<info>Database successfully synced.</info>");

        return self::SUCCESS;
    }
}

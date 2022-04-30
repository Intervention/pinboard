<?php

namespace Intervention\Pinboard\Commands;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PinboardAPI;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Intervention\Pinboard\Bookmark;
use Intervention\Pinboard\Tag;

class SyncCommand extends Command
{
    /**
     * Pinboard api connection
     *
     * @var \PinboardAPI
     */
    private $api;

    /**
     * Create new instance
     *
     * @param \PinboardAPI $api
     */
    public function __construct(PinboardAPI $api)
    {
        parent::__construct();

        $this->api = $api;
    }

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('sync');
        $this->setDescription('Sync local database with Pinboard account');
        $this->addOption('force', 'f', InputOption::VALUE_NONE);
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
        if ($this->bookmarksChanged() || $input->getOption('force')) {
            $pinboardData = $this->getPinboardData();

            if ($pinboardData->count()) {
                $output->writeln("<info>Sync started ...</info>");

                $this->resetDatabse($output);

                $bar = new ProgressBar($output, $pinboardData->count());
                $bar->start();

                foreach ($pinboardData as $pin) {
                    // save bookmark
                    $bookmark = Bookmark::create([
                        'title' => $pin->title,
                        'url' => $pin->url,
                        'timestamp' => $pin->timestamp
                    ]);

                    // save tags
                    foreach ($pin->tags as $tag) {
                        $bookmark->tags()->save(new Tag([
                            'title' => $tag
                        ]));
                    }

                    $bar->advance();
                }

                $bar->finish();
                echo PHP_EOL;

                $output->writeln("<info>Database successfully synced.</info>");
            } else {
                $output->writeln("<error>No bookmarks found in Pinboard account.</error>");
            }
        } else {
            $output->writeln("<info>No sync necessary. Database is up to date.</info>");
        }

        return 0;
    }

    /**
     * Reset database
     *
     * @return void
     */
    private function resetDatabse($output): void
    {
        $command = $this->getApplication()->find('setup');
        $command->run(new ArrayInput(['command' => 'setup']), $output);
    }

    /**
     * Determine if bookmarks have changed
     * and must be updated.
     *
     * @return boolean
     */
    private function bookmarksChanged()
    {
        $updated = Bookmark::lastUpdatedAt();
        $changed = Carbon::createFromTimestamp($this->api->get_updated_time());

        return $changed > $updated;
    }

    /**
     * Load Pinboard Bookmarks from configured account
     *
     * @param  integer $limit
     * @return PinboardCollection
     */
    private function getPinboardData($limit = null)
    {
        try {
            return new Collection($this->api->get_all($limit));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}

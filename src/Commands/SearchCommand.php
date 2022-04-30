<?php

namespace Intervention\Pinboard\Commands;

use Alfred\Workflows\Workflow as AlfredBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Intervention\Pinboard\Bookmark;

class SearchCommand extends Command
{
    /**
     * Alfred XML builder
     *
     * @var \Alfred\Workflows\Workflow
     */
    private $builder;

    /**
     * Create new instance
     *
     * @param AlfredBuilder $builder
     */
    public function __construct(AlfredBuilder $builder)
    {
        parent::__construct();

        $this->builder = $builder;
    }

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
                    $item = $this->builder->result();
                    $item->valid(true);
                    $item->uid('pinboard_bookmark');
                    $item->arg($bookmark->url);
                    $item->title($bookmark->title);
                    $item->subtitle($bookmark->url);
                    $item->icon(__DIR__ . '/../../storage/images/pinboard.png');
                }

                // output alfred xml
                exit($this->builder->output());
            }
        }

        return 0;
    }
}

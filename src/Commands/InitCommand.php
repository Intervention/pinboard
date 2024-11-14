<?php

declare(strict_types=1);

namespace Intervention\Pinboard\Commands;

use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends BaseCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('init');
        $this->setDescription('Init local database.');
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
        $builder = $this->app()->database()->schema();

        // bookmarks table
        $builder->dropIfExists('bookmarks');
        $builder->create('bookmarks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('url');
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamps();
            $table->index(['title']);
            $table->index(['url']);
        });

        // tags table
        $builder->dropIfExists('tags');
        $builder->create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bookmark_id');
            $table->string('title');
            $table->timestamps();
            $table->index(['bookmark_id']);
            $table->index(['title']);
        });

        if (!$this->app()->databaseIsFunctional()) {
            $output->writeln("<error>Failed to setup database correctly.</error>");
            return self::FAILURE;
        }

        $output->writeln("<info>Database successfully initiated.</info>");

        return self::SUCCESS;
    }
}

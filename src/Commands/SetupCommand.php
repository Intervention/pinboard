<?php

namespace Intervention\Pinboard\Commands;

use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Intervention\Pinboard\Bookmark;
use Intervention\Pinboard\Tag;

class SetupCommand extends Command
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('setup');
        $this->setDescription('Setup local database.');
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
        Capsule::statement("DROP TABLE IF EXISTS bookmarks");
        Capsule::statement("create table bookmarks (
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            title varchar NOT NULL, 
            url varchar NOT NULL, 
            timestamp integer NOT NULL, 
            created_at TIMESTAMP NOT NULL, 
            updated_at TIMESTAMP NOT NULL
        )");

        Capsule::statement("DROP INDEX IF EXISTS bookmarks_title_idx;");
        Capsule::statement("DROP INDEX IF EXISTS bookmarks_url_idx;");
        Capsule::statement("CREATE INDEX bookmarks_title_idx ON bookmarks (title);");
        Capsule::statement("CREATE INDEX bookmarks_url_idx ON bookmarks (url);");

        Capsule::statement("DROP TABLE IF EXISTS tags");
        Capsule::statement("create table tags (
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            bookmark_id integer NOT NULL, 
            title varchar NOT NULL, 
            created_at TIMESTAMP NOT NULL, 
            updated_at TIMESTAMP NOT NULL
        )");

        Capsule::statement("DROP INDEX IF EXISTS tags_bookmark_id_idx;");
        Capsule::statement("DROP INDEX IF EXISTS tags_title_idx;");
        Capsule::statement("CREATE INDEX tags_bookmark_id_idx ON tags (bookmark_id);");
        Capsule::statement("CREATE INDEX tags_title_idx ON tags (title);");

        if ($this->databaseExists()) {
            $output->writeln("<info>Database successfully initiated.</info>");
        } else {
            $output->writeln("<error>Failed to setup database correctly.</error>");
        }

        return 0;
    }

    /**
     * Determine if database exists
     *
     * @return bool
     */
    private function databaseExists()
    {
        try {
            if (is_numeric(Bookmark::count()) && is_numeric(Tag::count())) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }
}

<?php

declare(strict_types=1);

namespace Intervention\Pinboard;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use PinboardAPI;
use Intervention\Pinboard\Commands\InitCommand;
use Intervention\Pinboard\Commands\PullCommand;
use Intervention\Pinboard\Commands\SearchCommand;
use Intervention\Pinboard\Commands\StatusCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Illuminate\Database\Capsule\Manager as Capsule;
use Intervention\Pinboard\Models\Bookmark;
use Intervention\Pinboard\Models\Tag;

class Application extends BaseApplication
{
    protected Capsule $database;

    public function __construct(
        private string $api_username,
        private string $api_token,
        public string $database_filepath,
        public string $update_every = '12 hours',
    ) {
        parent::__construct('Intervention Pinboard');

        touch($this->database_filepath);
        $this->database = new Capsule();
        $this->database->bootEloquent();
        $this->database->setAsGlobal();
        $this->database->addConnection([
            'driver'    => 'sqlite',
            'database'  => $this->database_filepath,
            'prefix'    => '',
        ]);

        $this->add(new InitCommand());
        $this->add(new PullCommand());
        $this->add(new SearchCommand());
        $this->add(new StatusCommand());
    }

    /**
     * Return Pinboard API connection
     *
     * @return PinboardAPI
     */
    public function api(): PinboardAPI
    {
        return new PinboardAPI($this->api_username, $this->api_token);
    }

    /**
     * Return database connection
     *
     * @return Capsule
     */
    public function database(): Capsule
    {
        return $this->database;
    }

    /**
     * Determine if database is functional
     *
     * @return bool
     */
    public function databaseIsFunctional(): bool
    {
        try {
            if (is_numeric(Bookmark::count()) && is_numeric(Tag::count())) {
                return true;
            }
        } catch (Exception) {
            return false;
        }

        return false;
    }

    /**
     * Determine if database needs to be updated according to given interval string
     *
     * @throws InvalidFormatException
     * @throws InvalidCastException
     * @throws InvalidIntervalException
     * @throws UnitException
     * @return bool
     */
    public function databaseUpdateNecessary(): bool
    {
        return Bookmark::lastUpdatedAt() < Carbon::now()->sub(
            CarbonInterval::createFromDateString($this->update_every),
        );
    }
}

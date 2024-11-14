<?php

declare(strict_types=1);

namespace Intervention\Pinboard\Commands;

use DOMDocument;
use DOMElement;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Stream;
use Intervention\Pinboard\Models\Bookmark;
use Intervention\Pinboard\Models\Tag;
use PinboardBookmark;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends BaseCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('add');
        $this->setDescription('Add bookmark to Pinboard collection.');
        $this->addArgument('url', InputArgument::REQUIRED);
        $this->addArgument('tags', InputArgument::IS_ARRAY);
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
        // load html title
        try {
            $html = $this->loadHtml($input->getArgument('url'));
        } catch (Exception) {
            $output->writeln("<error>Unable to load contents from given url.</error>");
            return self::FAILURE;
        }

        // save local bookmark
        $model = Bookmark::firstOrCreate([
            'url' => $input->getArgument('url'),
        ], [
            'title' => $html->title(),
        ]);

        if (count($input->getArgument('tags'))) {
            $model->tags()->delete();
            // save local bookmark tags
            foreach ($input->getArgument('tags') as $tag) {
                if (!empty($tag)) {
                    $model->tags()->save(new Tag([
                        'title' => $tag
                    ]));
                }
            }
        }

        // create new api bookmark
        $bookmark = new PinboardBookmark();
        $bookmark->url = $input->getArgument('url');
        $bookmark->title = $html->title();
        $bookmark->description = $html->description();
        if (count($input->getArgument('tags'))) {
            $bookmark->tags = $input->getArgument('tags');
        }

        // save bookmark to api
        $this->app()->api()->save($bookmark);

        return self::SUCCESS;
    }

    /**
     * Load HTML contents from given url
     *
     * @param string $url
     * @return object
     * @throws GuzzleException
     */
    private function loadHtml(string $url): object
    {
        $response = $this->app()
            ->httpClient()
            ->get($url);

        return new class ($response->getBody())
        {
            protected DOMDocument $dom;

            public function __construct(Stream $contents)
            {
                $this->dom = new DOMDocument();
                @$this->dom->loadHTML((string) $contents);
            }

            public function title(): ?string
            {
                $nodes = $this->dom->getElementsByTagName('title');
                foreach ($nodes as $node) {
                    return $node->nodeValue;
                }

                return null;
            }

            public function description(): ?string
            {
                $nodes = $this->dom->getElementsByTagName('meta');
                foreach ($nodes as $node) {
                    if (is_a($node, DOMElement::class) && $node->getAttribute('name') === 'description') {
                        return $node->getAttribute('content');
                    }
                }

                return null;
            }
        };
    }
}

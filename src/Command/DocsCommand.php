<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Command;


use JsonException;
use Nelmio\ApiDocBundle\ApiDocGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\VarDumper;

class DocsCommand extends Command
{
    public static function getDefaultName(): string
    {
        return 'docs:json';
    }

    private ApiDocGenerator $apiDocGenerator;

    /**
     * DocsCommand constructor.
     * @param ApiDocGenerator $apiDocGenerator
     */
    public function __construct(ApiDocGenerator $apiDocGenerator)
    {
        $this->apiDocGenerator = $apiDocGenerator;

        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->setDescription('Print documentation.')
            ->addArgument('path', InputArgument::OPTIONAL, 'A path within doc to output. Defaults to root of the doc.')
            ->addOption('keys', 'k', InputOption::VALUE_NONE, 'Print only the keys of the doc at path')
            ->addOption('json', 'j', InputOption::VALUE_NONE, 'Print in json format');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        $doc = $this->apiDocGenerator->generate()->toArray();
        if ($path !== null) {
            $fields = array_filter(explode(':', $path));
            while (count($fields) && !empty($doc)) {
                $field = array_shift($fields);
                $doc = $doc[$field] ?? null;
            }
        }

        if ($input->getOption('keys')) {
            $doc = array_keys($doc);
        }
        if ($input->getOption('json')) {
            $output->write(json_encode($doc, JSON_THROW_ON_ERROR, 512));
        } else {
            VarDumper::dump($doc);
        }
        return 0;
    }
}

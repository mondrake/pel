<?php

namespace ExifEye\core\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ExifEye\core\Utility\SpecCompiler;

/**
 * A Symfony application command to compile the ExifEye specification YAML files.
 */
class CompileCommand extends Command
{
    /** @var string */
    private $resourceDirectory;

    /** @var string */
    private $specDirectory;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->specDirectory = (string) realpath(__DIR__ . '/../../../spec');
        $this->resourceDirectory = (string) realpath(__DIR__ . '/../../../resources');
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compile')
            ->setDescription('Converts the ExifEye specification YAML files into a spec.php file.')
            ->addArgument(
                'spec-dir',
                InputArgument::OPTIONAL,
                'Path to the directory of the .yaml specification files',
                $this->specDirectory
            )
            ->addArgument(
                'resource-dir',
                InputArgument::OPTIONAL,
                'Path to the directory of the spec.php file',
                $this->resourceDirectory
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $compiler = new SpecCompiler();
        $compiler->compile($input->getArgument('spec-dir'), $input->getArgument('resource-dir'));
    }
}

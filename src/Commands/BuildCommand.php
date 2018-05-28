<?php
/**
 * This file is part of the edd-static-generator package.
 *
 * (c) 2018 Eman Development & Design
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Commands;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class BuildCommand
 * @package App\Commands
 */
class BuildCommand extends Command
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BuildCommand constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this->setName('edd:build') // the name of the command (the part after "bin/console")
            ->setDescription('Build Pages') // the short description shown while running "php bin/console list"
            ->setHelp('This command will build the final product.'); // the full command description shown when running the command with the "--help" option
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try
        {
            $io->title('Page Generator');

            $io->section('Getting Page Data...');

            $io->section('Building Pages...');

            $io->section('Cleaning Up...');

            $io->success('Pages Generated Successfully.');
        }
        catch(\Throwable $t)
        {
            $io->error('Failed to generate pages, refer to log for full error details.');

            $this->logger->error($t);
        }
    }
}
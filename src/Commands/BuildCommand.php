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

use App\Services\PageBuilderService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class BuildCommand
 * @package App\Commands
 */
class BuildCommand extends ContainerAwareCommand
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PageBuilderService
     */
    private $pageBuilderService;

    /**
     * BuildCommand constructor.
     * @param LoggerInterface $logger
     * @param PageBuilderService $pageBuilderService
     */
    public function __construct(LoggerInterface $logger, PageBuilderService $pageBuilderService)
    {
        $this->logger = $logger;
        $this->pageBuilderService = $pageBuilderService;

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

            $this->GetPageList();

            $io->listing([]);

            if (!$io->confirm('Everything look correct above? (y/n)', false))
            {
                return;
            }

            $io->section('Building Pages...');

//             $this->getContainer()->get('twig')->render($view, $parameters);

            $io->section('Cleaning Up...');

            $io->success('Pages Generated Successfully.');
        }
        catch(\Throwable $t)
        {
            $io->error('Failed to generate pages, refer to log for full error details.');

            $this->logger->error($t);
        }
    }

//    private function GetPageList()
//    {
//        $templateRoot = $this->getContainer()->getParameter('twig.default_path') . '/Pages';
//
//        foreach (array_diff(scandir($templateRoot, SCANDIR_SORT_NONE), ['..', '.']) as $path)
//        {
//            //
//        }
//    }

    /**
     * @param string $dir
     * @param array $results
     * @return array
     */
    private function GetPageList($dir = '', array &$results = []) : array
    {
        foreach(array_diff(scandir($dir, SCANDIR_SORT_NONE), ['..', '.']) as $path)
        {
            if (is_dir($dir . $path . '/'))
            {
                $this->GetPageList($dir . $path . '/', $results);
            }
            else
            {
                $results[] = $dir . $path;
            }
        }

        return $results;
    }
}
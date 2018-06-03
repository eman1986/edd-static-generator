<?php
/**
 * This file is part of the edd-static-generator package.
 *
 * (c) 2018 Eman Development & Design
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Services;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PageBuilderService
 * @package App\Services
 */
class PageBuilderService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $pages = [];

    /**
     * @var string
     */
    protected $outputDir = 'output';

    /**
     * @var string
     */
    protected $templateRoot = '';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Set the $templateRoot member
     * @param string $templateRoot
     */
    public function SetTemplateRoot(string $templateRoot) : void
    {
        $this->templateRoot = $templateRoot;
    }

    /**
     * Compiles a list of pages to render and creates them
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function CompileList() : void
    {
        foreach($this->GetPageList($this->templateRoot) as $node)
        {
            $info = pathinfo($node);
            $path = str_replace('root', '/', $info['dirname']);
            $pathFiltered = substr($path, strpos($path, '/', 1));

            if ($info['filename'] === 'home')
            {
                $outputPath = $this->outputDir.$pathFiltered;
            }
            else
            {
                $outputPath = $this->outputDir.$pathFiltered . '/' . $info['filename'];
            }

            if (!file_exists($outputPath) && !mkdir($outputPath, 0755, true)
                && !is_dir($outputPath))
            {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $outputPath));
            }

            $this->CreatePage($this->templateRoot . '/' . $info['filename'], $outputPath);
        }
    }

    /**
     * Creates page from template.
     * @param string $templatePath
     * @param string $outputPath
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function CreatePage(string $templatePath, string $outputPath) : void
    {
        $pg = $this->container->get('twig')->render($templatePath, []);

        file_put_contents($outputPath. '/index.htm', $pg);
    }

    /**
     * Compiles a list of files & folders
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
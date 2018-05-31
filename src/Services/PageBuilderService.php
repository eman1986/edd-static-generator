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

    public function CompileList(bool $dryRun = false)
    {
        foreach($this->GetPageList($this->templateRoot) as $node)
        {
            $info = pathinfo($node);
            $path = str_replace('root', '/', $info['dirname']);
            $pathFiltered = substr($path, strpos($path, '/', 1));

            if ($info['filename'] === 'home')
            {
                $completePath = $this->outputDir.$pathFiltered;
            }
            else
            {
                $completePath = $this->outputDir.$pathFiltered.'/'.$info['filename'];
            }

            if (!file_exists($completePath) && !mkdir($completePath, 075, true)
                && !is_dir($completePath))
            {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $completePath));
            }

            $this->CreatePage($completePath . '/index.htm', $this->templateRoot);
        }
    }

    /**
     * Create page
     * @param string $completePath
     * @param string $content
     */
    public function CreatePage(string $completePath, string $content) : void
    {
        try
        {
            $pg = $this->container->get('twig')->render($completePath, []);
        }
        catch (\Twig_Error_Loader $e)
        {
        }
        catch (\Twig_Error_Runtime $e)
        {
        }
        catch (\Twig_Error_Syntax $e)
        {
        }


        file_put_contents($completePath. '/index.htm', $content);
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
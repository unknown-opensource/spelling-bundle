<?php

namespace Unknown\Bundle\SpellingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Unknown\Bundle\SpellingBundle\Checker\CoreChecker;

class TwigSpellcheckCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('twig:spellcheck')
            ->setDescription('Spellcheck templates')
            ->addArgument('locale', InputArgument::OPTIONAL, 'Locale', 'lt');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer(); /* @var $container ContainerInterface */
        $kernel = $container->get('kernel'); /* @var $kernel KernelInterface */
        $sources = $kernel->getRootDir()."/../src/";
        $sources = realpath($sources);
        $bundles = $kernel->getBundles();
        foreach ($bundles as $bundle) {
            if (strpos($bundle->getPath(), $sources) === false) {
                continue;
            }
            $this->checkDirectory($bundle->getPath()."/Resources/views", $input->getArgument('locale'));
        }
    }

    protected function checkDirectory($path, $locale)
    {
        if (!file_exists($path)) {
            return;
        }
        $dp = opendir($path);
        while ($item = readdir($dp)) {
            if ($item{0} == ".") {
                continue;
            }
            $name = $path."/".$item;
            if (is_dir($name)) {
                $this->checkDirectory($name, $locale);
            }
            if (is_file($name)) {
                $this->checkTemplate($name, $locale);
            }
        }
    }

    protected function checkTemplate($fullName, $locale)
    {
        if (!strpos($fullName, ".html.twig")) {
            return;
        }
        $contents = file_get_contents($fullName);
        $contents = preg_replace('/\{\{.*\}\}/sU', ' ', $contents);
        $contents = preg_replace('/\{\%.*\%\}/sU', ' ', $contents);
        $contents = preg_replace('/\{\#.*\#\}/sU', ' ', $contents);
        $contents = "<br/>".$contents;
        $checker = new CoreChecker($contents, $locale);
        $hasErrors = $checker->hasErrors();
        if ($hasErrors) {
            print $fullName."\n";
            foreach ($checker->getErrors() as $error) {
                print "> ".$error."\n";
            }
        }
    }
}

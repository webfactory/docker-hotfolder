<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Deletes outdated archived uploads to free disc space.
 */
final class PurgeCommand extends Command
{
    protected static $defaultName = 'hotfolder:purge';

    protected function configure()
    {
        $this
            ->setDescription('Purge old files from the archive directory')
            ->addArgument('path', InputArgument::REQUIRED, 'Archive directory path');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $path = rtrim($input->getArgument('path'), '/').'/';

        $this->log('Starting on %s', $path);

        $files = (new Finder())->files()
                    ->in($path)
                    ->depth(0)
                    ->date('< now - 7 days');

        foreach ($files as $file) {
            unlink($file->getPathname());
            $this->log('Purged %s', $file);
        }

        $this->log('Finished on %s', $path);
    }

    private function log(string $message, ...$args): void
    {
        $this->output->writeln(vsprintf(self::$defaultName.': '.$message, $args));
    }
}

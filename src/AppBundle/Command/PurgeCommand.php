<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Deletes outdated archived uploads to free disc space.
 */
#[AsCommand(name: 'hotfolder:purge', description: 'Purge old files from the archive directory')]
final class PurgeCommand extends Command
{
    private OutputInterface $output;

    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::REQUIRED, 'Archive directory path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
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

        return 0;
    }

    private function log(string $message, ...$args): void
    {
        $this->output->writeln(vsprintf($this->getName().': '.$message, $args));
    }
}

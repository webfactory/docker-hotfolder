<?php

namespace AppBundle\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UploadCommand extends Command
{
    protected static $defaultName = 'hotfolder:upload';

    private string $url;

    private string $hotfolder;

    private string $pattern;

    private string $archive;

    private string $formFieldName;

    private OutputInterface $output;

    protected function configure(): void
    {
        $this->setName('hotfolder:upload')
            ->setDescription('Submit new files using HTTP POST')
            ->addOption('form-field-name', null, InputOption::VALUE_REQUIRED, 'Name for the multipart form field upload', 'file')
            ->addArgument('url', InputArgument::REQUIRED, 'Target URL')
            ->addArgument('hotfolder', InputArgument::REQUIRED, 'Hotfolder directory')
            ->addArgument('pattern', InputArgument::REQUIRED, 'Filename pattern to process')
            ->addArgument('archive', InputArgument::REQUIRED, 'Archive directory');
    }

    private function log(string $message, ...$args): void
    {
        $this->output->writeln(vsprintf(self::$defaultName.': '.$message, $args));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        $this->url = $input->getArgument('url');

        $this->hotfolder = $this->ensureTrailingSlash($input->getArgument('hotfolder'));
        $this->pattern = $input->getArgument('pattern');
        $this->archive = $this->ensureTrailingSlash($input->getArgument('archive'));
        $this->formFieldName = $input->getOption('form-field-name');

        $this->log('Starting on %s with pattern %s', $this->hotfolder, $this->pattern);

        /** @var SplFileInfo $file */
        foreach ($this->getFilesToUpload() as $file) {
            $this->log('Found file %s', $file);
            $this->waitForFile($file);
            $this->upload($file);
        }

        $this->log('Finished on %s', $this->hotfolder);

        return 0;
    }

    private function ensureTrailingSlash(string $path): string
    {
        return rtrim($path, \DIRECTORY_SEPARATOR).\DIRECTORY_SEPARATOR;
    }

    /**
     * @return \Traversable<SplFileInfo>
     */
    private function getFilesToUpload(): \Traversable
    {
        $finder = new Finder();

        return $finder->files()
            ->in($this->hotfolder)
            ->depth(0)
            ->name($this->pattern)
            ->sortByModifiedTime();
    }

    private function waitForFile(SplFileInfo $file): void
    {
        $minAge = 30;

        do {
            clearstatcache(false, $file);
            $currAge = time() - $file->getMTime();

            if ($currAge > $minAge) {
                break;
            }

            $waitFor = $minAge - $currAge + 1;
            $this->log('File %s is newer than %d seconds, waiting %s more seconds', $file, $minAge, $waitFor);
            sleep($waitFor);
        } while (true);
    }

    private function upload(SplFileInfo $file): void
    {
        try {
            $client = new Client(['base_uri' => $this->url]);
            $client->request(
                'POST',
                '',
                [
                    'multipart' => [[
                        'name' => $this->formFieldName,
                        'contents' => fopen($file, 'r'),
                        'filename' => $file->getFilename(),
                    ]],
                ]
            );
            $this->log('Successfully POSTed %s to %s', $file, $this->url);
            $this->moveToArchive($file);
        } catch (GuzzleException $exception) {
            $this->log('Error uploading %s: %s', $file, $exception);
        }
    }

    private function moveToArchive(\SplFileInfo $file): void
    {
        $target = $this->archive.$file->getFilename().'.'.uniqid();

        if (!rename($file, $target)) {
            $this->log('Failed to archive %s  as %s', $file, $target);
        } else {
            $this->log('Archived %s as %s', $file, $target);
        }
    }
}

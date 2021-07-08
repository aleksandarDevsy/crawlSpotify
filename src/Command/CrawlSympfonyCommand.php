<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\Services\CrawlerSpotifyJob;
use App\Services\SpotifyJobExperienceService;

class CrawlSympfonyCommand extends Command
{
    protected static $defaultName = 'crawl:spotify';
    protected static $defaultDescription = 'Get jobs from Spotify for Sweeden';

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $crawlerSpotifyJob = new CrawlerSpotifyJob("https://www.lifeatspotify.com");
        $jobs = $crawlerSpotifyJob->listJobs();
        foreach($jobs as $key => $job)
        {
            $checkWords = ['experienced','deep knowledge','experience','leadership']; // list of words
            $crawlerSpotifyJobExperienceService = new SpotifyJobExperienceService($job['url']);
            $jobs[$key]['desc'] = $crawlerSpotifyJob->getJobDescription($job);
            $jobs[$key]['yearsExperience'] = $crawlerSpotifyJobExperienceService->getYearsOfExperience();
            $jobs[$key]['experienced'] = $crawlerSpotifyJobExperienceService->determineExperiencedJob($checkWords);
        }
       
        $io = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();
        try {
            $filesystem->dumpFile('public/jobs.json', json_encode($jobs));
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your file ".$exception->getPath();
        }        

        return Command::SUCCESS;
    }
}

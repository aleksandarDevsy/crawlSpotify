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
use App\Services\CrawlerSpotifyJobInterface;
use App\Services\SpotifyJobExperienceServiceInterface;

class CrawlSympfonyCommand extends Command
{
    protected static $defaultName = 'crawl:spotify';
    protected static $defaultDescription = 'Get jobs from Spotify for Sweeden';
    const EXPERIENCE_KEYWORDS = ['experienced','deep knowledge','experience','leadership'];

    function __construct(CrawlerSpotifyJobInterface $crawlerSpotifyJob,SpotifyJobExperienceServiceInterface $spotifyJobExperienceService) {
        $this->crawlerSpotifyJob = $crawlerSpotifyJob;
        $this->spotifyJobExperienceService = $spotifyJobExperienceService;
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jobs = $this->crawlerSpotifyJob->listJobs();
        foreach($jobs as $key => $job)
        {
            $jobContent = $this->crawlerSpotifyJob->getContentData($job['url']);
            $jobs[$key]['desc'] = $this->crawlerSpotifyJob->getJobDescription($job);
            $jobs[$key]['yearsExperience'] = $this->spotifyJobExperienceService->getYearsOfExperience($jobContent);
            $jobs[$key]['experienced'] = $this->spotifyJobExperienceService->determineExperiencedJob(self::EXPERIENCE_KEYWORDS,$jobContent);
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

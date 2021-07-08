<?php

namespace App\Services;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerSpotifyJob
{
    function __construct($url) {
        $this->url = $url;
        $response = Browsershot::url($url.'/jobs')                
                ->windowSize(1200, 800)
                ->click(".select_arrow__2COPq",'left',1,1000) // open select input
                ->delay(2000)
                ->setOption('addScriptTag', json_encode(['content' => 'document.querySelectorAll("input[type=\'checkbox\'][value=\'stockholm\']")[0].click();document.querySelectorAll("input[type=\'checkbox\'][value=\'gothenburg\']")[0].click();setTimeout(function(){ while( document.querySelectorAll(".buttons_filled__3PgIs")[0] != undefined) document.querySelectorAll(".buttons_filled__3PgIs")[0].click();},5000);'])) 
                ->delay(7000) // wait until we check options Stockholm and Gothenburg and click on load more button until it is visible
                ->bodyHtml(); // get response back
        
        $this->data = $response;
    }

    function listJobs()
    {
        $crawler = new Crawler($this->data);
        // filter and iterate jobs https://www.lifeatspotify.com/jobs after applied filters for  Stockholm and Gothenburg and load all jobs 
        $jobs = $crawler->filter('.entry_container__1pt_- > .entry_header__2Rw2O')->each(function (Crawler $node, $i) {
            $job = [];
            // get name and url from 
            $job['name'] = $node->filter('a')->text(); // get name
            $job['url'] = $this->url.$node->filter('a')->attr('href'); // get url
            $job['desc'] = '';
            return $job;
        });

        return $jobs;
    }

    function getJobDescription($job)
    {
        $desc = '';
        // open url to get description for job and years of experience
        $descHtml = \file_get_contents($job['url']);
        $descCraw = new Crawler($descHtml);
        // Get text from singlejob_introText__2Qm_D
        if($descCraw->filter('.singlejob_introText__2Qm_D > div > span')->count())
            $desc = $descCraw->filter('.singlejob_introText__2Qm_D > div > span')->text();
        // Get text from  singlejob_descriptionText__2M45Z
        if($descCraw->filter('.singlejob_descriptionText__2M45Z > div > span')->count())
            $desc.= $descCraw->filter('.singlejob_descriptionText__2M45Z > div > span')->text();
        
        return $desc;
    }
}

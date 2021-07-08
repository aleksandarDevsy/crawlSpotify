<?php

namespace App\Services;


interface CrawlerSpotifyJobInterface
{

    function listJobs();
  
    function getJobDescription($job);

    function getContentData($url);
}

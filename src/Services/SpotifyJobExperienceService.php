<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;

class SpotifyJobExperienceService {

    function __construct($url) {
        $data = new Crawler(\file_get_contents($url));
        // Get text from lists from job page
        $text = $data->filter('ul.list_list__mHc5U')->each(function (Crawler $node, $i){
            return $node->text();
        });
        $this->data = implode(" ",$text);
    }

    public function getYearsOfExperience()
    {
        preg_match('/\d+(?:\+|-\d+)?\s+years?\b/', $this->data, $output_array);
        if(!empty($output_array))
            return $output_array[0];
    }

    public function determineExperiencedJob($checkWords)
    {
        $experienced = false;
        
        // Scan text for words that guess if job is for experienced professionals. 
        foreach($checkWords as $cw)
        {
            if(strpos($this->data,$cw) !== false)
            {
                $experienced = true;
                break;
            }
        }
        return $experienced;
    }

}
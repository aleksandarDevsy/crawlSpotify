<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;

class SpotifyJobExperienceService implements SpotifyJobExperienceServiceInterface {
   

    public function getYearsOfExperience($data)
    {
        preg_match('/\d+(?:\+|-\d+)?\s+years?\b/', $data, $output_array);
        if(!empty($output_array))
            return $output_array[0];
    }

    public function determineExperiencedJob($checkWords,$data)
    {
        $experienced = false;
        
        // Scan text for words that guess if job is for experienced professionals. 
        foreach($checkWords as $cw)
        {
            if(strpos($data,$cw) !== false)
            {
                $experienced = true;
                break;
            }
        }
        return $experienced;
    }

}
<?php

namespace App\Services;


interface SpotifyJobExperienceServiceInterface
{
    public function getYearsOfExperience($data);
    
    public function determineExperiencedJob($checkWords,$data);
   
}

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
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class CrawlSympfonyCommand extends Command
{
    protected static $defaultName = 'crawl:spotify';
    protected static $defaultDescription = 'Get jobs from Spotify for Sweeden';

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
        $url = "https://www.lifeatspotify.com/jobs";
        $io = new SymfonyStyle($input, $output);
        $response = Browsershot::url($url)                
        ->windowSize(1200, 800)
            ->click(".select_arrow__2COPq",'left',1,1000) // open select input
            ->delay(2000)
            ->setOption('addScriptTag', json_encode(['content' => 'document.querySelectorAll("input[type=\'checkbox\'][value=\'stockholm\']")[0].click();document.querySelectorAll("input[type=\'checkbox\'][value=\'gothenburg\']")[0].click();setTimeout(function(){ while( document.querySelectorAll(".buttons_filled__3PgIs")[0] != undefined) document.querySelectorAll(".buttons_filled__3PgIs")[0].click();},5000);'])) 
            ->delay(7000) // wait until we check options Stockholm and Gothenburg and click on load more button until it is visible
            ->bodyHtml(); // get response back


        $crawler = new Crawler($response);
        // filter and iterate jobs 
        $crawler = $crawler->filter('.entry_container__1pt_-')->each(function (Crawler $node, $i) {
            $job = [];
            $childrens = $node->children(); // for each job get childrens so we can get url and name
            foreach($childrens as $ch) 
            {
                if(in_array('entry_header__2Rw2O',explode(" ",$ch->getAttribute('class')))) // locate div 
                {
                    $job['name'] = $ch->childNodes[0]->textContent; // get name
                    $job['url'] = $ch->childNodes[0]->getAttribute('href'); // get url

                    // open url to get description for job and years of experience
                    $descHtml = \file_get_contents('https://www.lifeatspotify.com'.$job['url']);
                    $descCraw = new Crawler($descHtml);
                    // scan html that is in ".singlejob_introText__2Qm_D" div  and  ".singlejob_descriptionText__2M45Z" div for description 
                /* $job['desc'] = array_merge ( $descCraw->filter('.singlejob_introText__2Qm_D > div')->each(function (Crawler $node, $i){
                        return strip_tags($node->html());
                    }),
                    $descCraw->filter('.singlejob_descriptionText__2M45Z > div')->each(function (Crawler $node, $i){
                        return strip_tags($node->html());
                    })); */
                    // Search if in text is mentioned years of so we can get years of experience
                    $job['yearsExperience'] = array_values(array_filter($descCraw->filter('ul.list_list__mHc5U')->each(function (Crawler $node, $i){
                        $ye = strstr( $node->getNode(0)->textContent, 'years of',true);
                        if($ye !== false)
                        return $ye. ' years of experience';
                    })));
                    $job['yearsExperience'] = end($job['yearsExperience']);

                    // Scan text for words that guess if job is for experienced professionals. List can be extended with words 
                    $job['experienced'] = max ($descCraw->filter('ul.list_list__mHc5U')->each(function (Crawler $node, $i){
                        $checkWords = ['experienced','deep knowledge','experience','leadership']; // list of words
                        foreach($checkWords as $cw)
                        {
                            if(strpos($node->getNode(0)->textContent,$cw) !== false)
                            {
                                return  1;
                            }
                        }
                        return 0;
                    }));
                }
                
            }
            return $job;
        });
        
        $filesystem = new Filesystem();

        try {
            $filesystem->dumpFile('public/jobs.json', json_encode($crawler));
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your file ".$exception->getPath();
        }        

        return Command::SUCCESS;
    }
}

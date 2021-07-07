<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;


class SpotifyController extends AbstractController
{
    /**
     * @Route("/spotify", name="spotify")
     */
    
    public function index(): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'crawl:spotify'
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();
        return new Response($content);
    }
}

<?php

namespace App\Controller;

use App\Model\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NavBarController extends AbstractController
{
    public function __invoke(): Response
    {
        $movies = MovieRepository::list();

        return $this->render('navbar.html.twig', [
            'movies' => $movies,
        ]);
    }
}

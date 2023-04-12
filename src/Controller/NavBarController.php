<?php

namespace App\Controller;

use App\Model\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NavBarController extends AbstractController
{
    public function __invoke(MovieRepository $movieRepository): Response
    {
        $movies = Movie::fromEntities($movieRepository->findAll());

        return $this->render('navbar.html.twig', [
            'movies' => $movies,
        ]);
    }
}

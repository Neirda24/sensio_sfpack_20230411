<?php

namespace App\Controller;

use App\Model\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NavBarController extends AbstractController
{
    public function __invoke(MovieRepository $movieRepository, string $currentRoute, ?string $currentSlug): Response
    {
        $movies = Movie::fromEntities($movieRepository->listAll());

        return $this->render('navbar.html.twig', [
            'movies' => $movies,
            'currentRoute' => $currentRoute,
            'currentSlug' => $currentSlug,
        ]);
    }
}

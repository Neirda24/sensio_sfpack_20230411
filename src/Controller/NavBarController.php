<?php

namespace App\Controller;

use App\Model\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NavBarController extends AbstractController
{
    public function __construct(
        private readonly MovieRepository $movieRepository,
    )
    {
    }

    public function __invoke(string $currentRoute, ?string $currentSlug): Response
    {
        $movies = Movie::fromEntities($this->movieRepository->listAll());

        return $this->render('navbar.html.twig', [
            'movies' => $movies,
            'currentRoute' => $currentRoute,
            'currentSlug' => $currentSlug,
        ]);
    }
}

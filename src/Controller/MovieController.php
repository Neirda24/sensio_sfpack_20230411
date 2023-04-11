<?php

namespace App\Controller;

use App\Model\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    #[Route(
        '/movies',
        name: 'movie_list',
        methods: ['GET']
    )]
    public function list(): Response
    {
        $movies = MovieRepository::list();

        return $this->render('movie/list.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route(
        '/movie/{slug}',
        name: 'movie_details',
        requirements: [
            'slug' => '[a-zA-Z0-9-_]{3,}'
        ],
        methods: ['GET']
    )]
    public function details(string $slug): Response
    {
        $movie = MovieRepository::get($slug);

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }
}

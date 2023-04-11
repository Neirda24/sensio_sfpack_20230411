<?php

namespace App\Controller;

use App\Model\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    #[Route(
        '/movie/{slug}',
        name: 'movie_details',
        requirements: [
            'slug' => '\w+'
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

<?php

namespace App\Controller;

use App\Entity\Movie as MovieEntity;
use App\Form\MovieType;
use App\Model\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    #[Route(
        '/movies',
        name: 'movie_list',
        methods: ['GET']
    )]
    public function list(MovieRepository $movieRepository): Response
    {
        $movies = Movie::fromEntities($movieRepository->listAll());

        return $this->render('movie/list.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route(
        '/movie/{slug}',
        name: 'movie_details',
        requirements: [
            'slug' => Movie::SLUG_REGEX,
        ],
        methods: ['GET']
    )]
    public function details(MovieRepository $movieRepository, string $slug): Response
    {
        try {
            $movie = Movie::fromEntity($movieRepository->getBySlug($slug));
        } catch (NoResultException $e) {
            throw $this->createNotFoundException('Movie not found', previous: $e);
        }

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route(
        '/movie/new',
        name: 'movie_new',
        methods: ['GET', 'POST'],
        priority: 10
    )]
    #[Route(
        '/movie/{slug}/edit',
        name: 'movie_edit',
        requirements: [
            'slug' => Movie::SLUG_REGEX,
        ],
        methods: ['GET', 'POST']
    )]
    public function newOrEdit(Request $request, MovieRepository $movieRepository, ?string $slug = null): Response
    {
        $movieEntity = new MovieEntity();

        if (null !== $slug) {
            try {
                $movieEntity = $movieRepository->getBySlug($slug);
            } catch (NoResultException $e) {
                throw $this->createNotFoundException('Movie not found', previous: $e);
            }
        }

        $form = $this->createForm(MovieType::class, $movieEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movieRepository->save($movieEntity, true);

            return $this->redirectToRoute('movie_details', ['slug' => $movieEntity->getSlug()]);
        }

        return $this->render('movie/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Movie as MovieEntity;
use App\Form\MovieType;
use App\Model\Movie;
use App\Omdb\Client\NoResultException as OmdbNoResultException;
use App\Omdb\Client\OmdbApiConsumerInterface;
use App\Repository\MovieRepository;
use Doctrine\ORM\NoResultException as DoctrineNoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    public function __construct(
        private readonly MovieRepository          $movieRepository,
        private readonly OmdbApiConsumerInterface $omdbApiConsumer,
    )
    {
    }

    #[Route(
        '/movies',
        name: 'movie_list',
        methods: ['GET']
    )]
    public function list(): Response
    {
        $movies = Movie::fromEntities($this->movieRepository->listAll());

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
    public function details(string $slug): Response
    {
        try {
            $movie = Movie::fromEntity($this->movieRepository->getBySlug($slug));
        } catch (DoctrineNoResultException $doctrineNotFound) {
            try {
                $movie = Movie::fromOmdbResult($this->omdbApiConsumer->getById($slug));
            } catch (OmdbNoResultException $omdbNotFound) {
                throw $this->createNotFoundException('Movie not found', previous: $omdbNotFound);
            }
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
    public function newOrEdit(Request $request, ?string $slug = null): Response
    {
        $movieEntity = new MovieEntity();

        if (null !== $slug) {
            try {
                $movieEntity = $this->movieRepository->getBySlug($slug);
            } catch (DoctrineNoResultException $e) {
                throw $this->createNotFoundException('Movie not found', previous: $e);
            }
        }

        $form = $this->createForm(MovieType::class, $movieEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->movieRepository->save($movieEntity, true);

            return $this->redirectToRoute('movie_details', ['slug' => $movieEntity->getSlug()]);
        }

        return $this->render('movie/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

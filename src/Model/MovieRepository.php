<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @phpstan-type RawMovieData array{slug: string, title: string, plot: string, releasedAt: string, poster: string, genres: list<string>}
 */
final class MovieRepository
{
    /**
     * @var list<RawMovieData>
     */
    private const LIST = [
        [
            'slug'       => 'avatar',
            'title'      => 'avatar',
            'plot'       => "Malgré sa paralysie, Jake Sully, un ancien marine immobilisé dans un fauteuil roulant, est resté un combattant au plus profond de son être. Il est recruté pour se rendre à des années-lumière de la Terre, sur Pandora, où de puissants groupes industriels exploitent un minerai rarissime destiné à résoudre la crise énergétique sur Terre. Parce que l'atmosphère de Pandora est toxique pour les humains, ceux-ci ont créé le Programme Avatar, qui permet à des \" pilotes\" humains de lier leur esprit à un avatar, un corps biologique commandé à distance, capable de survivre dans cette atmosphère létale. Ces avatars sont des hybrides créés génétiquement en croisant l'ADN humain avec celui des Na'vi, les autochtones de Pandora.
Sous sa forme d'avatar, Jake peut de nouveau marcher. On lui confie une mission d'infiltration auprès des Na'vi, devenus un obstacle trop conséquent à l'exploitation du précieux minerai. Mais tout va changer lorsque Neytiri, une très belle Na'vi, sauve la vie de Jake...",
            'releasedAt' => '16/12/2009',
            'poster'     => 'avatar.webp',
            'genres'     => ['Action', 'Adventure', 'Fantasy'],
        ],
        [
            'slug'       => 'asterix-et-obelix_mission-cleopatre',
            'title'      => 'Astérix et Obélix : Mission Cléopâtre',
            'plot'       => "Cléopâtre, la reine d’Égypte, décide, pour défier l'Empereur romain Jules César, de construire en trois mois un palais somptueux en plein désert. Si elle y parvient, celui-ci devra concéder publiquement que le peuple égyptien est le plus grand de tous les peuples. Pour ce faire, Cléopâtre fait appel à Numérobis, un architecte d'avant-garde plein d'énergie. S'il réussit, elle le couvrira d'or. S'il échoue, elle le jettera aux crocodiles.
Celui-ci, conscient du défi à relever, cherche de l'aide auprès de son vieil ami Panoramix. Le druide fait le voyage en Égypte avec Astérix et Obélix. De son côté, Amonbofis, l'architecte officiel de Cléopâtre, jaloux que la reine ait choisi Numérobis pour construire le palais, va tout mettre en œuvre pour faire échouer son concurrent.",
            'releasedAt' => '30/01/2002',
            'poster'     => 'mission-cleopatre.jpg',
            'genres'     => ['Documentary', 'Adventure', 'Comedy', 'Family'],
        ],
    ];
}

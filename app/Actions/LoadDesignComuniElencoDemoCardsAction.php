<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

/**
 * Card elenco statiche del reference Design Comuni (tab Elenco).
 *
 * @see https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html
 */
final class LoadDesignComuniElencoDemoCardsAction
{
    /**
     * @return array<int, object{
     *     id: null,
     *     name: string,
     *     content: string,
     *     type_label: string,
     *     location: array{address: string},
     *     demo_images: int
     * }>
     */
    public function execute(): array
    {
        return [
            (object) [
                'id' => null,
                'name' => 'Buca in via Solferino',
                'content' => 'Sulla strada c’è una buca piuttosto profonda che andrebbe sistemata con urgenza',
                'type_label' => 'Verde pubblico e arredo urbano',
                'location' => ['address' => 'Via Solferino - 50100 Firenze'],
                'demo_images' => 3,
            ],
            (object) [
                'id' => null,
                'name' => 'Titolo segnalazione 2',
                'content' => 'Sulla strada c’è una buca piuttosto profonda che andrebbe sistemata con urgenza',
                'type_label' => 'Tipologia segnalazione',
                'location' => ['address' => 'Via Solferino - 50100 Firenze'],
                'demo_images' => 0,
            ],
            (object) [
                'id' => null,
                'name' => 'Titolo segnalazione 3',
                'content' => 'Sulla strada c’è una buca piuttosto profonda che andrebbe sistemata con urgenza',
                'type_label' => 'Tipologia segnalazione',
                'location' => ['address' => 'Via Solferino - 50100 Firenze'],
                'demo_images' => 0,
            ],
        ];
    }
}

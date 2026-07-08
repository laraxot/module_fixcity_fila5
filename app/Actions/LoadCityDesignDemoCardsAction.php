<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

/**
 * Static demo cards from Design Comuni reference (List tab).
 *
 * @see https://italia.github.io/design-comuni-pagine-statiche/sito/ticket-list.html
 */
final class LoadCityDesignDemoCardsAction
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
                'name' => 'Pothole in via Solferino',
                'content' => 'There is a rather deep pothole on the road that should be fixed urgently',
                'type_label' => 'Public green and urban furniture',
                'location' => ['address' => 'Via Solferino - 50100 Florence'],
                'demo_images' => 3,
            ],
            (object) [
                'id' => null,
                'name' => 'Ticket Title 2',
                'content' => 'There is a rather deep pothole on the road that should be fixed urgently',
                'type_label' => 'Ticket Type',
                'location' => ['address' => 'Via Solferino - 50100 Florence'],
                'demo_images' => 0,
            ],
            (object) [
                'id' => null,
                'name' => 'Ticket Title 3',
                'content' => 'There is a rather deep pothole on the road that should be fixed urgently',
                'type_label' => 'Ticket Type',
                'location' => ['address' => 'Via Solferino - 50100 Florence'],
                'demo_images' => 0,
            ],
        ];
    }
}

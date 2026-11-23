<?php

declare(strict_types=1);

use Illuminate\Auth\Access\AuthorizationException;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Observers\TicketMediaObserver;
use Modules\Fixcity\Tests\TestCase;
use Modules\Media\Models\Media;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Role;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use PHPUnit\Framework\Assert;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

uses(TestCase::class);

/** @see docs/stories/dcp-379-foto-nel-dettaglio-della-segnalazione.story.md */
it('registers a JPEG PNG only completion photo collection', function (): void {
    $ticket = new Ticket;
    $collection = $ticket->getRegisteredMediaCollections()->firstWhere('name', 'completion_photo');

    Assert::assertNotNull($collection);
    if (! $collection instanceof MediaCollection) {
        throw new RuntimeException('Collection completion_photo non registrata.');
    }
    Assert::assertSame(['image/jpeg', 'image/png'], $collection->acceptsMimeTypes);
});

/** @see docs/stories/dcp-379-foto-nel-dettaglio-della-segnalazione.story.md */
it('denies completion photo insertion for an unresolved ticket and a nonstaff citizen', function (): void {
    $operator = UserFactory::new()->createOne();
    $operator->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));
    $citizen = UserFactory::new()->createOne();

    $pending = TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING]);
    $resolved = TicketFactory::new()->createOne(['status' => TicketStatusEnum::RESOLVED]);

    $mediaFor = static function (Ticket $ticket): Media {
        $media = new Media;
        $media->model_type = $ticket->getMorphClass();
        $media->model_id = SafeStringCastAction::cast($ticket->getKey());
        $media->collection_name = 'completion_photo';
        $media->disk = 'public';

        return $media;
    };

    $this->actingAs($operator);
    expect(fn () => app(TicketMediaObserver::class)->creating($mediaFor($pending)))
        ->toThrow(AuthorizationException::class);

    $this->actingAs($citizen);
    expect(fn () => app(TicketMediaObserver::class)->creating($mediaFor($resolved)))
        ->toThrow(AuthorizationException::class);
});

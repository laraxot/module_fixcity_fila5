<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Fixcity\Actions\ResolveTicketTypeMarkerPropertiesAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Media\Models\Media;
use Modules\User\Models\User;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Actions\File\AssetAction;
use Modules\Xot\Contracts\ProfileContract;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Datas\XotData;
use Modules\Fixcity\Models\Concerns\HasTicketRelations;
use Modules\Fixcity\Models\Concerns\InteractsWithTicketCitizenRating;
use Modules\Fixcity\Models\Concerns\NormalizesTicketLocation;
use Modules\Comment\Models\Concerns\HasComments;
use Modules\Comment\Models\CommentNotificationSubscription;
use Modules\Comment\Models\Reaction;
use Modules\Comment\Models\Contracts\Commentable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\ModelStatus\HasStatuses;
use Spatie\ModelStatus\Status;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Webmozart\Assert\Assert;

/**
 * Modules\Fixcity\Models\Ticket.
 *
 * @property string $name
 * @property string $slug
 * @property int $id
 * @property string $content
 * @property int $owner_id
 * @property int|null $responsible_id
 * @property int $status_id
 * @property string|null $code
 * @property string|null $ticket_prefix
 * @property int $order
 * @property int $priority_id
 * @property int|null $project_id
 * @property float|null $estimation
 * @property int|null $epic_id
 * @property int|null $sprint_id
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $type_id
 * @property TicketTypeEnum|null $type
 * @property string|null $latitude
 * @property string|null $longitude
 * @property array<string, mixed>|null $location
 * @property string|null $updated_by
 * @property string|null $created_by
 * @property string|null $deleted_by
 * @property Collection<int, TicketActivity> $activities
 * @property int|null $activities_count
 * @property Collection<int, \Modules\Comment\Models\Comment> $comments
 * @property int|null $comments_count
 * @property Collection<int, TicketComment> $ticketComments Legacy relation, do not use in new FO flows.
 * @property int|null $ticket_comments_count
 * @property mixed $completude_percentage
 * @property mixed $estimation_for_humans
 * @property mixed $estimation_in_seconds
 * @property mixed $estimation_progress
 * @property Collection<int, TicketHour> $hours
 * @property int|null $hours_count
 * @property MediaCollection<int, Media> $media
 * @property int|null $media_count
 * @property User|null $owner
 * @property User|null $assignee
 * @property TicketPriorityEnum|null $priority
 * @property Collection<int, TicketRelation> $relations
 * @property int|null $relations_count
 * @property User|null $responsible
 * @property TicketStatusEnum|null $status
 * @property Collection<int, User> $ticketSubscribers
 * @property int|null $ticket_subscribers_count
 * @property mixed $total_logged_hours
 * @property mixed $total_logged_in_hours
 * @property mixed $total_logged_seconds
 *
 * @method static TicketFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereEpicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereEstimation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereResponsibleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereSprintId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereTicketPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket withoutTrashed()
 *
 * @property Collection<int, Status> $statuses
 * @property int|null $statuses_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket currentStatus(...$names)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket otherCurrentStatus(...$names)
 *
 * @property ProfileContract|null $creator
 * @property ProfileContract|null $updater
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereType($value)
 *
 * @property Collection<int, CommentNotificationSubscription> $notificationSubscriptions
 * @property int|null $notification_subscriptions_count
 * @property Profile|null $deleter
 *
 * @mixin \Eloquent
 */
class Ticket extends BaseModel implements Commentable, HasMedia
{
    use HasComments;
    use HasSlug;
    use HasStatuses;
    use InteractsWithMedia;
    use HasTicketRelations;
    use InteractsWithTicketCitizenRating;
    use NormalizesTicketLocation;

    protected $fillable = [
        'name',
        'content',
        'email',
        'owner_id',
        'responsible_id',
        'project_id',
        'code',
        'order',
        'estimation',
        'epic_id',
        'sprint_id',
        'latitude',
        'longitude', // GEO
        'location',
        // 'status_id', 'type_id', 'priority_id', //OLD
        'status',
        'type',
        'type_id',
        'priority',
        'slug',
        'citizen_rating',
        'citizen_rated_at',
    ];

    protected $appends = [
        // 'estimationInSeconds',
        // 'estimationProgress',
    ];

    /** @return Attribute<string, never> */
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $type = $this->type ?? $this->type_id;

                if ($type instanceof TicketTypeEnum) {
                    return $type->getLabel();
                }

                if (\is_string($type) && $type !== '') {
                    try {
                        return TicketTypeEnum::from($type)->getLabel();
                    } catch (\ValueError) {
                        return $type;
                    }
                }

                return '';
            },
        );
    }

    public function casts(): array
    {
        return [
            'location' => 'array',
            'estimationInSeconds' => 'int',
            'estimationProgress' => 'float',
            'status' => TicketStatusEnum::class,
            'type' => TicketTypeEnum::class,
            'type_id' => TicketTypeEnum::class,
            'citizen_rated_at' => 'datetime',
            'citizen_rating' => 'integer',
        ];
    }

    public function resolveTicketStatusValue(): string
    {
        $currentStatus = $this->currentStatus();
        if (is_object($currentStatus) && isset($currentStatus->name) && is_string($currentStatus->name)) {
            return $currentStatus->name;
        }

        $raw = $this->getRawOriginal('status');

        return is_string($raw) ? $raw : '';
    }

    public function isOwnedByAuthenticatedUser(): bool
    {
        $uid = auth()->id();
        if ($uid === null) {
            return false;
        }

        if ($this->owner_id !== null && (string) $this->owner_id === (string) $uid) {
            return true;
        }

        return in_array((string) $uid, [(string) $this->created_by, (string) $this->updated_by], true);
    }

    public function isVisibleOnPublicFrontoffice(): bool
    {
        $statusValue = $this->resolveTicketStatusValue();
        if ($statusValue !== '') {
            $status = TicketStatusEnum::tryFrom($statusValue);
            if ($status instanceof TicketStatusEnum && in_array($status, TicketStatusEnum::canViewByAll(), true)) {
                return true;
            }
        }

        return auth()->check() && $this->isOwnedByAuthenticatedUser();
    }

    /**
     * @return array{url: string, type: 'svg', scale: array{0: int, 1: int}}|array{}
     */
    public function getIconData(): array
    {
        $type = $this->type ?? $this->type_id;
        if ($type === null) {
            return [];
        }

        Assert::isInstanceOf($type, TicketTypeEnum::class, '['.__LINE__.']['.__FILE__.']');
        $url = app(ResolveTicketTypeMarkerPropertiesAction::class)->execute($type)['iconUrl'];

        return [
            'url' => $url,
            'type' => 'svg',
            'scale' => [35, 35],
        ];
    }

    /**
     * @return array{lat: string, lng: string}
     */
    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'latitude',
            'lng' => 'longitude',
        ];
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            // ->generateSlugsFrom(['type', 'name'])
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50)
            ->usingSeparator('_');
    }

    // Slug generation handled by Spatie HasSlug trait via getSlugOptions().
    // Removed getSlugAttribute() that caused circular update during create.

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function (Ticket $ticket): void {
            if (! $ticket->status) {
                $ticket->status = TicketStatusEnum::PENDING;
            }
        });
    }

    /**
     * @return Attribute<float|int, never>
     */
    public function totalLoggedInHours(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                return (float) $this->hours()->sum('value');
            },
        );
    }

    /**
     * @return Attribute<string, never>
     */
    public function estimationForHumans(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $seconds = $this->estimation_in_seconds;
                $secondsInt = is_numeric($seconds) ? (int) $seconds : 0;

                return CarbonInterval::seconds($secondsInt)->cascade()->forHumans();
            },
        );
    }

    /**
     * This string will be used in notifications on what a new comment
     * was made.
     */
    public function commentableName(): string
    {
        return 'Segnalazione';
    }

    /**
     * This URL will be used in notifications to let the user know
     * where the comment itself can be read.
     */
    public function commentUrl(): string
    {
        $path = '/tickets/'.SafeStringCastAction::cast($this->getKey());
        $localized = LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getCurrentLocale(),
            $path
        );

        return is_string($localized) && $localized !== '' ? $localized : url($path);
    }

    /**
     * Set the status of the ticket.
     */
    public function setStatus(string|TicketStatusEnum $status): void
    {
        if (\is_string($status)) {
            $status = TicketStatusEnum::tryFrom($status);
        }

        if ($status instanceof TicketStatusEnum) {
            $this->status = $status;
            $this->save();
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);
        $this->addMediaCollection('ticket')
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);
        // ->maxFileSize(10 * 1024 * 1024); // 10MB
    }

    /**
     * Mostra il prompt valutazione al proprietario se il ticket è risolto/chiuso e non ancora valutato.
     */
    public function needsCitizenRatingPrompt(): bool
    {
        if ($this->citizen_rating !== null) {
            return false;
        }

        if (! auth()->check()) {
            return false;
        }

        if (! $this->isOwnedByAuthenticatedUser()) {
            return false;
        }

        $statusValue = $this->resolveTicketStatusValue();
        if ($statusValue === '') {
            return false;
        }

        $status = TicketStatusEnum::tryFrom($statusValue);

        return $status === TicketStatusEnum::RESOLVED || $status === TicketStatusEnum::CLOSED;
    }
}

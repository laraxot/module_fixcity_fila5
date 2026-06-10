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
use Modules\Fixcity\Models\Concerns\InteractsWithTicketCitizenRating;
use Modules\Comment\Models\Concerns\HasComments;
use Modules\Comment\Models\CommentNotificationSubscription;
use Modules\Comment\Models\Reaction;
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
 * @property Collection<int, TicketComment> $ticketComments
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
 * @property Collection<int, User> $subscribers
 * @property int|null $subscribers_count
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
class Ticket extends BaseModel implements HasMedia
{
    use HasComments;
    use HasSlug;
    use HasStatuses;
    use InteractsWithMedia;
    use InteractsWithTicketCitizenRating;

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
        /*
        static::creating(function (Ticket $item) {
            $project = Project::where('id', $item->project_id)->first();
            $count = Ticket::where('project_id', $project->id)->count();
            $order = $project->tickets?->last()?->order ?? -1;
            $item->code = $project->ticket_prefix . '-' . ($count + 1);
            $item->order = $order + 1;
        });
        */
        // static::created(function (Ticket $item) {
        //     Assert::notNull($item->sprint);
        //     if ($item->sprint_id && $item->sprint->epic_id) {
        //         Ticket::where('id', $item->id)->update(['epic_id' => $item->sprint->epic_id]);
        //     }
        //     foreach ($item->watchers ?? [] as $user) {
        //         $user->notify(new TicketCreated($item));
        //     }
        // });

        // static::updating(function (Ticket $item) {
        //     $old = Ticket::firstWhere(['id' => $item->id]);

        //     // Ticket activity based on status
        //     $oldStatus = $old?->status_id;
        //     if ($oldStatus != $item->status_id) {
        //         Assert::notNull(auth()->user());
        //         TicketActivity::create([
        //             'ticket_id' => $item->id,
        //             'old_status_id' => $oldStatus,
        //             'new_status_id' => $item->status_id,
        //             'user_id' => authId(),
        //         ]);
        //         /*
        //         foreach ($item->watchers as $user) {
        //             $user->notify(new TicketStatusUpdated($item));
        //         }
        //             */
        //     }

        //     // Ticket sprint update
        //     $oldSprint = $old?->sprint_id;
        //     if ($oldSprint && ! $item->sprint_id) {
        //         Ticket::where('id', $item->id)->update(['epic_id' => null]);
        //     } elseif ($item->sprint_id && $item->sprint?->epic_id) {
        //         Ticket::where('id', $item->id)->update(['epic_id' => $item->sprint->epic_id]);
        //     }
        // });
    }

    /**
     * @return BelongsTo<Model&UserContract, $this>
     */
    public function owner(): BelongsTo
    {
        $user_class = XotData::make()->getUserClass();

        return $this->belongsTo($user_class, 'owner_id', 'id');
    }

    /**
     * @return BelongsTo<Model&UserContract, $this>
     */
    public function responsible(): BelongsTo
    {
        $user_class = XotData::make()->getUserClass();

        return $this->belongsTo($user_class, 'responsible_id', 'id');
    }

    // public function status(): BelongsTo
    // {
    //     return $this->belongsTo(TicketStatus::class, 'status_id', 'id')->withTrashed();
    // }

    // public function project(): BelongsTo
    // {
    //     return $this->belongsTo(Project::class, 'project_id', 'id')->withTrashed();
    // }

    // public function type(): BelongsTo
    // {
    //    return $this->belongsTo(TicketType::class, 'type_id', 'id')->withTrashed();
    // }

    // public function priority(): BelongsTo
    // {
    //    return $this->belongsTo(TicketPriority::class, 'priority_id', 'id')->withTrashed();
    // }

    /**
     * @return HasMany<TicketActivity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class, 'ticket_id', 'id');
    }

    /*-- e' in comment
    public function subscribers(): BelongsToMany
    {
        $user_class = XotData::make()->getUserClass();

        return $this->belongsToMany($user_class, 'ticket_subscribers', 'ticket_id', 'user_id');
    }
    */
    /**
     * @return HasMany<TicketRelation, $this>
     */
    public function relations(): HasMany
    {
        return $this->hasMany(TicketRelation::class, 'ticket_id', 'id');
    }

    /**
     * @return HasMany<TicketHour, $this>
     */
    public function hours(): HasMany
    {
        return $this->hasMany(TicketHour::class, 'ticket_id', 'id');
    }

    // public function epic(): BelongsTo
    // {
    //     return $this->belongsTo(Epic::class, 'epic_id', 'id');
    // }

    // public function sprint(): BelongsTo
    // {
    //     return $this->belongsTo(Sprint::class, 'sprint_id', 'id');
    // }

    // public function sprints(): BelongsTo
    // {
    //     return $this->belongsTo(Sprint::class, 'sprint_id', 'id');
    // }

    /*
    public function watchers(): Attribute
    {
        return new Attribute(
            get: function () {
                $users = $this->project->profiles;
                $users->push($this->owner);
                if ($this->responsible) {
                    $users->push($this->responsible);
                }

                return $users->unique('id');
            }
        );
    }
    */

    // public function totalLoggedHours(): Attribute
    // {
    //     return new Attribute(
    //         get: function () {
    //             $seconds = $this->hours->sum('value') * 3600;

    //             return CarbonInterval::seconds($seconds)->cascade()->forHumans();
    //         }
    //     );
    // }

    // public function totalLoggedSeconds(): Attribute
    // {
    //     return new Attribute(
    //         get: function () {
    //             return $this->hours->sum('value') * 3600;
    //         }
    //     );
    // }

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
    /*
    public function estimationInSeconds(): Attribute
    {
        return new Attribute(
            get: function(): ?int {
                if (! $this->estimation) {
                    return null;
                }

                return $this->estimation * 3600;
            }
        );
    }
    */

    /*
    public function estimationProgress(): Attribute
    {
        return new Attribute(
            get: function(): float {
                return (($this->totalLoggedSeconds ?? 0) / ($this->estimationInSeconds ?? 1)) * 100;
            }
        );
    }
    */

    /*
    public function completudePercentage(): Attribute
    {
        return new Attribute(
            get: fn () => $this->estimationProgress
        );
    }

    */

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
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        /** @var class-string<User> $userModel */
        $userModel = config('auth.providers.users.model');

        return $this->belongsTo($userModel, 'assignee_id');
    }

    /**
     * Commenti legacy admin (tabella ticket_comments).
     *
     * @return HasMany<TicketComment, $this>
     */
    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
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

    /**
     * @return BelongsToMany<User, $this>
     */
    public function subscribers(): BelongsToMany
    {
        /** @var class-string<User> $userModel */
        $userModel = config('auth.providers.users.model');

        return $this->belongsToMany($userModel, 'ticket_subscribers')
            ->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);
        $this->addMediaCollection('ticket')
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);
        // ->maxFileSize(10 * 1024 * 1024); // 10MB
    }

    /*
     * Canonical source of truth is the JSON `location` payload.
     * Legacy `latitude` / `longitude` columns are mirrored for backward compatibility.
     *
     * @return Attribute<array<string, mixed>, array<string, mixed>>

    protected function location(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): array {
                $location = [];

                if (\is_string($value) && $value !== '') {
                    $decoded = \json_decode($value, true);
                    if (\is_array($decoded)) {
                        $location = $decoded;
                    }
                } elseif (\is_array($value)) {
                    $location = $value;
                }

                $location['lat'] = self::normalizeCoordinateString($location['lat'] ?? $location['latitude'] ?? $attributes['latitude'] ?? null);
                $location['lng'] = self::normalizeCoordinateString($location['lng'] ?? $location['longitude'] ?? $attributes['longitude'] ?? null);
                $location['address'] = self::normalizeText($location['address'] ?? $location['display_name'] ?? null);
                $location['provider'] = self::normalizeNullableText($location['provider'] ?? null);

                return array_filter(
                    $location,
                    static fn (mixed $item): bool => $item !== null && $item !== ''
                );
            },
            set: function (mixed $value): array {
                if (! is_array($value)) {
                    return [];
                }

                $location = $value;
                $location['lat'] = self::normalizeCoordinateString($value['lat'] ?? $value['latitude'] ?? null);
                $location['lng'] = self::normalizeCoordinateString($value['lng'] ?? $value['longitude'] ?? null);
                $location['address'] = self::normalizeNullableText($value['address'] ?? $value['display_name'] ?? null);
                $location['provider'] = self::normalizeNullableText($value['provider'] ?? null);

                $location = array_merge($location, self::extractAddressComponents(self::stringKeyed($value)));

                unset($location['latitude'], $location['longitude'], $location['address_components'], $location['addressdetails']);

                $location = array_filter(
                    $location,
                    static fn (mixed $item): bool => $item !== null && $item !== ''
                );

                $payload = [];

                if (self::hasTableColumn('latitude')) {
                    $payload['latitude'] = $location['lat'] ?? null;
                }

                if (self::hasTableColumn('longitude')) {
                    $payload['longitude'] = $location['lng'] ?? null;
                }

                if (self::hasTableColumn('location')) {
                    $payload['location'] = $location !== [] ? \json_encode($location) : null;
                }

                return $payload;
            },
        );
    }
        */

    /**
     * @param  array<string, mixed>  $value
     * @return array<string, string|array<string, mixed>|null>
     */
    public static function extractAddressComponents(array $value): array
    {
        $details = $value['address_components'] ?? $value['addressdetails'] ?? $value['address_details'] ?? null;
        if (! \is_array($details) || $details === []) {
            return [];
        }

        /** @var array<string, mixed> $filtered */
        $filtered = array_filter([
            'street' => self::normalizeNullableText($value['street'] ?? $details['street'] ?? null),
            'street_number' => self::normalizeNullableText($value['street_number'] ?? $details['house_number'] ?? null),
            'zip' => self::normalizeNullableText($value['zip'] ?? $value['postcode'] ?? $details['postcode'] ?? null),
            'postcode' => self::normalizeNullableText($value['postcode'] ?? $details['postcode'] ?? null),
            'city' => self::normalizeNullableText($value['city'] ?? $details['city'] ?? $details['village'] ?? $details['municipality'] ?? null),
            'province' => self::normalizeNullableText($value['province'] ?? $details['county'] ?? $details['state_district'] ?? null),
            'state' => self::normalizeNullableText($value['state'] ?? $details['state'] ?? $details['region'] ?? null),
            'country' => self::normalizeNullableText($value['country'] ?? $details['country'] ?? null),
            'country_code' => self::normalizeNullableText($value['country_code'] ?? $details['country_code'] ?? null),
            'suburb' => self::normalizeNullableText($value['suburb'] ?? $details['suburb'] ?? $details['neighbourhood'] ?? null),
            'address_details' => $details,
        ], static fn (mixed $item): bool => $item !== null && $item !== '');

        /** @var array<string, string|null> $result */
        $result = [];
        foreach ($filtered as $key => $value) {
            $result[$key] = is_string($value) ? $value : null;
        }

        return $result;
    }

    public static function normalizeCoordinateString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (\is_int($value) || \is_float($value) || (\is_string($value) && is_numeric($value))) {
            return (string) $value;
        }

        return null;
    }

    private static function normalizeText(mixed $value): string
    {
        return \is_string($value) ? trim($value) : '';
    }

    private static function normalizeNullableText(mixed $value): ?string
    {
        $normalized = self::normalizeText($value);

        return $normalized !== '' ? $normalized : null;
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

    /*
    private static function hasTableColumn(string $column): bool
    {
        $model = new self();
        $connection = (string) $model->getConnectionName();
        $table = $model->getTable();
        $cacheKey = $connection.'|'.$table.'|'.$column;

        if (array_key_exists($cacheKey, self::$columnAvailabilityCache)) {
            return self::$columnAvailabilityCache[$cacheKey];
        }

        self::$columnAvailabilityCache[$cacheKey] = Schema::connection($connection)->hasColumn($table, $column);

        return self::$columnAvailabilityCache[$cacheKey];
    }
        */

    /**
     * @param  array<mixed>  $value
     * @return array<string, mixed>
     */
    public static function stringKeyed(array $value): array
    {
        $normalized = [];

        foreach ($value as $key => $item) {
            if (\is_string($key)) {
                $normalized[$key] = $item;
            }
        }

        return $normalized;
    }
}

<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Modules\Media\Models\Media;
use Modules\User\Models\User;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Builder;
use Spatie\ModelStatus\Status;
use Modules\Xot\Contracts\ProfileContract;
use Spatie\Comments\Models\CommentNotificationSubscription;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Notifications\TicketCreated;
use Modules\Fixcity\Notifications\TicketStatusUpdated;
use Modules\Xot\Actions\File\AssetAction;
use Modules\Xot\Datas\XotData;
use Modules\Xot\Models\XotBaseModel;
use Spatie\Comments\Models\Concerns\HasComments;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\ModelStatus\HasStatuses;
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
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $updated_by
 * @property string|null $created_by
 * @property string|null $deleted_by
 * @property Collection<int, TicketActivity> $activities
 * @property int|null $activities_count
 * @property Collection<int, TicketComment> $comments
 * @property int|null $comments_count
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
 * @property TicketTypeEnum|null $type
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
 *
 * @mixin \Eloquent
 */
class Ticket extends XotBaseModel implements HasMedia
{
    use HasComments;
    use HasSlug;
    use HasStatuses;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'content',
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
        // 'status_id', 'type_id', 'priority_id', //OLD
        'status',
        'type',
        'priority',
        'slu  g',
    ];

    protected $appends = [
        // 'estimationInSeconds',
        // 'estimationProgress',
    ];



    public function casts(): array
    {
        return [
            'estimationInSeconds' => 'int',
            'estimationProgress' => 'float',
            'status' => TicketStatusEnum::class,
            'priority' => TicketPriorityEnum::class,
            'type' => TicketTypeEnum::class,
        ];
    }

    /**
     * @return \Modules\Fixcity\Database\Factories\TicketFactory
     */
    protected static function newFactory(): \Modules\Fixcity\Database\Factories\TicketFactory
    {
        return \Modules\Fixcity\Database\Factories\TicketFactory::new();
    }

    public function getIconData(): array
    {
        if ($this->type == null) {
            return [];
        }

        Assert::isInstanceOf($this->type, TicketTypeEnum::class, '[' . __LINE__ . '][' . __FILE__ . ']');
        $url = $this->type->getIcon();
        $url = Str::of((string) $url)->after('heroicon-o-')->append('.svg')->toString();
        $url = app(AssetAction::class)->execute('ui::svg/' . $url);

        return [
            'url' => $url,
            'type' => 'svg',
            'scale' => [35, 35],
        ];
    }

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

    public function getSlugAttribute(?string $value): ?string
    {
        if ($value != null) {
            return $value;
        }
        $value = Str::of($this->name)->slug()->toString();
        $this->update(['slug' => $value]);

        return $value;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function (Ticket $ticket) {
            if (!$ticket->status) {
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

    public function owner(): BelongsTo
    {
        $user_class = XotData::make()->getUserClass();

        return $this->belongsTo($user_class, 'owner_id', 'id');
    }

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
    public function relations(): HasMany
    {
        return $this->hasMany(TicketRelation::class, 'ticket_id', 'id');
    }

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

    public function totalLoggedInHours(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->hours->sum('value');
            }
        );
    }

    public function estimationForHumans(): Attribute
    {
        return new Attribute(
            get: function () {
                return CarbonInterval::seconds($this->estimation_in_seconds)->cascade()->forHumans();
            }
        );
    }
    /*
    public function estimationInSeconds(): Attribute
    {
        return new Attribute(
            get: function (): ?int {
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
            get: function (): float {
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
        return '#';
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
     * @return HasMany<TicketComment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * Set the status of the ticket.
     */
    public function setStatus(string|TicketStatusEnum $status): void
    {
        if (is_string($status)) {
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
        // ->maxFileSize(10 * 1024 * 1024); // 10MB
    }
}

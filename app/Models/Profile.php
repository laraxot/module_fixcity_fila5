<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

// use GeneaLabs\LaravelModelCaching\CachedBuilder;
use Modules\Xot\Contracts\UserContract;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\SchemalessAttributes;
use Modules\User\Models\DeviceUser;
use Modules\User\Models\Device;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Modules\Media\Models\Media;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\DatabaseNotification;
use Modules\User\Models\Team;
use Modules\Fixcity\Database\Factories\ProfileFactory;
use Modules\User\Models\Membership;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Modules\User\Models\BaseProfile as UserBaseProfile;
use Modules\User\Models\Permission;
// use Modules\Xot\Models\Traits\WidgetTrait;
use Modules\User\Models\Role;
use Modules\User\Models\SocialiteUser;

/**
 * Modules\Fixcity\Models\Profile.
 *
 * @property string|null $full_name
 * @property Collection<int, Permission> $permissions
 * @property int|null $permissions_count
 * @property Collection<int, Role> $roles
 * @property int|null $roles_count
 * @property UserContract|null $user
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $updated_by
 * @property string|null $created_by
 * @method static Builder|Profile whereCreatedAt($value)
 * @method static Builder|Profile whereCreatedBy($value)
 * @method static Builder|Profile whereFirstName($value)
 * @method static Builder|Profile whereId($value)
 * @method static Builder|Profile whereLastName($value)
 * @method static Builder|Profile whereUpdatedAt($value)
 * @method static Builder|Profile whereUpdatedBy($value)
 * @property Collection<int, TicketHour> $hours
 * @property int|null $hours_count
 * @property Collection<int, SocialiteUser> $socials
 * @property int|null $socials_count
 * @property Collection<int, Ticket> $ticketsOwned
 * @property int|null $tickets_owned_count
 * @property Collection<int, Ticket> $ticketsResponsible
 * @property int|null $tickets_responsible_count
 * @property mixed $total_logged_in_hours
 * @property string $user_id
 * @property string|null $email
 * @property string $credits
 * @property string|null $slug
 * @property SchemalessAttributes|null $extra
 * @property Carbon|null $deleted_at
 * @property string|null $deleted_by
 * @property string $avatar
 * @property Collection<int, DeviceUser> $deviceUsers
 * @property int|null $device_users_count
 * @property Collection<int, Device> $devices
 * @property int|null $devices_count
 * @property MediaCollection<int, Media> $media
 * @property int|null $media_count
 * @property Collection<int, DeviceUser> $mobileDeviceUsers
 * @property int|null $mobile_device_users_count
 * @property Collection<int, Device> $mobileDevices
 * @property int|null $mobile_devices_count
 * @property DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property int|null $notifications_count
 * @property Collection<int, Team> $teams
 * @property int|null $teams_count
 * @property string|null $user_name
 * @method static ProfileFactory factory($count = null, $state = [])
 * @method static Builder|Profile whereCredits($value)
 * @method static Builder|Profile whereDeletedAt($value)
 * @method static Builder|Profile whereDeletedBy($value)
 * @method static Builder|Profile whereEmail($value)
 * @method static Builder|Profile whereExtra($value)
 * @method static Builder|Profile whereSlug($value)
 * @method static Builder|Profile whereUserId($value)
 * @method static Builder|\Modules\User\Models\BaseProfile withExtraAttributes()
 * @method static Builder|\Modules\User\Models\BaseProfile withoutPermission($permissions)
 * @method static Builder|\Modules\User\Models\BaseProfile withoutRole($roles, $guard = null)
 * @property DeviceUser $pivot
 * @property Membership $membership
 * @property \Modules\Fixcity\Models\Profile|null $creator
 * @property \Modules\Fixcity\Models\Profile|null $updater
 * @property-read Profile|null $deleter
 * @method static Builder<static>|Profile byUuid(string $uuid)
 * @method static Builder<static>|Profile childrenWith(array $relations)
 * @method static Builder<static>|Profile childrenWithCount(array $relations)
 * @method static Builder<static>|Profile newModelQuery()
 * @method static Builder<static>|Profile newQuery()
 * @method static Builder<static>|Profile permission($permissions, bool $without = false)
 * @method static Builder<static>|Profile query()
 * @method static Builder<static>|Profile role($roles, ?string $guard = null, bool $without = false)
 * @mixin \Eloquent
 */
class Profile extends UserBaseProfile
{
    /** @var string */
    protected $connection = 'fixcity';

    /** @var list<string> */
    protected $fillable = ['id', 'user_id', 'phone', 'email', 'bio'];

    // ------- RELATIONSHIP ----------

    // public function projectsOwning(): HasMany
    // {
    //     return $this->hasMany(Project::class, 'owner_id', 'user_id');
    // }

    // public function projectsAffected(): BelongsToMany
    // {
    //     return $this->belongsToMany(Project::class, 'project_users', 'user_id', 'project_id')->withPivot(['role']);
    // }

    // public function favoriteProjects(): BelongsToMany
    // {
    //     return $this->belongsToMany(Project::class, 'project_favorites', 'user_id', 'project_id');
    // }

    public function ticketsOwned(): HasMany
    {
        return $this->hasMany(Ticket::class, 'owner_id', 'user_id');
    }

    public function ticketsResponsible(): HasMany
    {
        return $this->hasMany(Ticket::class, 'responsible_id', 'user_id');
    }

    public function socials(): HasMany
    {
        return $this->hasMany(SocialiteUser::class, 'user_id', 'user_id');
    }

    public function hours(): HasMany
    {
        return $this->hasMany(TicketHour::class, 'user_id', 'user_id');
    }

    public function totalLoggedInHours(): Attribute
    {
        return new Attribute(
            get: fn () => $this->hours->sum('value')
        );
    }
}// end model

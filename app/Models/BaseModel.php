<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Xot\Actions\Factory\GetFactoryAction;
use Modules\Xot\Traits\Updater;

/**
 * Class BaseModel.
 */
abstract class BaseModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    // use Searchable;
    // use Cachable;
    use Updater;

    /**
     * Indicates whether attributes are snake cased on arrays.
     *
     * @see  https://laravel-news.com/6-eloquent-secrets
     */
    public static bool $snakeAttributes = true;

    public bool $incrementing = true;

    public bool $timestamps = true;

    protected int $perPage = 30;

    protected $connection = 'fixcity';

    /** @var list<string> */
    protected $fillable = ['id'];

    /**
     * @var array<string>
     */
    protected array $dates = ['published_at', 'created_at', 'updated_at'];

    protected string $primaryKey = 'id';

    /** @var list<string> */
    protected array $hidden = [
        // 'password'
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            // 'published_at' => 'datetime:Y-m-d', // da verificare
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return app(GetFactoryAction::class)->execute(static::class);
    }
}

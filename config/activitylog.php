<?php

use Modules\Activity\Models\Activity;

return [

    /*
     * If set to false, no activities will be saved to the database.
     */
    'enabled' => (config('env.ACTIVITY_LOGGER_ENABLED') ?? true),

    /*
     * Days an activity log row is kept: `activitylog:clean` (scheduled daily in routes/console.php) deletes older rows.
     * 365 = the 12 months the privacy notice (lang/it/policy.md, "Periodo di conservazione") promises.
     * spatie/laravel-activitylog v5 reads `clean_after_days`; the v4 key `delete_records_older_than_days` is no longer read.
     */
    'clean_after_days' => 365,

    /*
     * If no log name is passed to the activity() helper
     * we use this default log name.
     */
    'default_log_name' => 'default',

    /*
     * You can specify an auth driver here that gets user models.
     * If this is null we'll use the current Laravel auth driver.
     */
    'default_auth_driver' => null,

    /*
     * If set to true, the subject returns soft deleted models.
     */
    'subject_returns_soft_deleted_models' => false,

    /*
     * This model will be used to log activity.
     * It should implement the Spatie\Activitylog\Contracts\Activity interface
     * and extend Illuminate\Database\Eloquent\Model.
     */
    'activity_model' => Activity::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Activity model shipped with this package.
     */
    'table_name' => 'activity_log',

    /*
     * This is the database connection that will be used by the migration and
     * the Activity model shipped with this package. In case it's not set
     * Laravel's database.default will be used instead.
     */
    'database_connection' => config('env.ACTIVITY_LOGGER_DB_CONNECTION'),
];

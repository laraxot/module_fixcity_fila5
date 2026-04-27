<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Contracts\UserContract;
use Modules\User\Models\Policies\UserBasePolicy;

class TicketPolicy extends UserBasePolicy
{
   
}

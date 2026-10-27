<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Filament;

use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Modules\Fixcity\Tests\TestCase;
use Modules\Xot\Filament\Traits\HasXotTable;

uses(TestCase::class);
/*
 * DCP-181 (audit Filament, A-1): l'azione di eliminazione di gruppo autorizzava una sola volta con `deleteAny()`, che molte policy del progetto non
 * definiscono. La regola per record deve negare dove la policy nega, e lasciare passare i modelli senza policy come fa Filament.
 */
final class BulkDeleteProbeModel extends Model
{
    protected $guarded = [];
}

final class BulkDeleteProbePolicy
{
    public function delete(?object $user, BulkDeleteProbeModel $record): bool
    {
        return (bool) $record->getAttribute('deletable');
    }
}

final class BulkDeleteUnpolicedModel extends Model
{
    protected $guarded = [];
}

it('denies a record the policy refuses and allows the one it accepts', function (): void {
    Gate::policy(BulkDeleteProbeModel::class, BulkDeleteProbePolicy::class);
    $rule = HasXotTable::bulkDeleteRecordAuthorization();

    expect($rule(new BulkDeleteProbeModel(['deletable' => false])))->toBeFalse();
    expect($rule(new BulkDeleteProbeModel(['deletable' => true])))->toBeTrue();
});

it('keeps a model without policy allowed, like Filament, instead of denying it', function (): void {
    expect(Gate::getPolicyFor(BulkDeleteUnpolicedModel::class))->toBeNull();

    $rule = HasXotTable::bulkDeleteRecordAuthorization();

    expect($rule(new BulkDeleteUnpolicedModel))->toBeTrue();
});

it('is applied to every bulk delete action, including the ones created without the Xot table base', function (): void {
    Gate::policy(BulkDeleteProbeModel::class, BulkDeleteProbePolicy::class);
    $action = DeleteBulkAction::make('probe');

    $denied = $action->getIndividualRecordAuthorizationResponse(new BulkDeleteProbeModel(['deletable' => false]));
    $allowed = $action->getIndividualRecordAuthorizationResponse(new BulkDeleteProbeModel(['deletable' => true]));

    expect($denied->allowed())->toBeFalse();
    expect($allowed->allowed())->toBeTrue();
});

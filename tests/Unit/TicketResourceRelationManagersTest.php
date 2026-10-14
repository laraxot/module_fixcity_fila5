<?php

declare(strict_types=1);

use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Filament\Resources\TicketResource\RelationManagers\CommentsRelationManager;
use PHPUnit\Framework\Assert;

/*
 * Audit Blueprint F-01 (DCP-180): XotBaseResource::getRelations() scopre per glob ogni *RelationManager.php
 * nella cartella del resource, quindi un file dimenticato li' diventa un relation manager attivo.
 * MediaRelationManager caricava un FileUpload con preserveFilenames sul disco public (servito via /storage) ma la
 * tabella media non ha le colonne `file` e `description`: il file restava su disco con il nome del client, il salvataggio falliva.
 * Solo reflection, nessun database.
 */
it('registers only relation managers that can persist their form', function (): void {
    Assert::assertSame([CommentsRelationManager::class], TicketResource::getRelations());
});

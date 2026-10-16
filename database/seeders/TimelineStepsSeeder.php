<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Models\TimelineStep;

class TimelineStepsSeeder extends Seeder
{
    public function run(): void
    {
        $scenarios = [
            ['type' => 'email_reminder', 'description' => 'Sollecito invio risposta', 'responsible' => 'ULSS', 'performed_at' => now()->subDays(5), 'requires_followup' => true, 'followup_at' => now()->addDays(3), 'metadata' => ['subject' => 'Sollecito ticket #DMO-0042']],
            ['type' => 'photo_added', 'description' => 'Foto aggiuntive caricate', 'responsible' => 'Comune', 'performed_at' => now()->subDays(2), 'requires_followup' => false, 'metadata' => ['count' => 3]],
            ['type' => 'response_negative', 'description' => 'Risposta negativa del cittadino', 'responsible' => 'Supporto', 'performed_at' => now()->subDays(3)],
            ['type' => 'response_delegated', 'description' => 'Risposta delegata alla vigile', 'responsible' => 'Vigili', 'performed_at' => now()->subDays(8)],
            ['type' => 'response_soon', 'description' => 'Risposta in attesa di conferma', 'responsible' => 'Comune', 'performed_at' => now()->subDay(), 'requires_followup' => true, 'followup_at' => now()->addDays(2)],
            ['type' => 'closed_resolved', 'description' => 'Chiusura risolta', 'responsible' => 'ULSS', 'performed_at' => now()->subDays(10)],
            ['type' => 'closed_rejected', 'description' => 'Chiusura respinta', 'responsible' => 'Moderatore X', 'performed_at' => now()->subDays(6)],
            ['type' => 'closed_delegated', 'description' => 'Chiusura delegata', 'responsible' => 'Vigili', 'performed_at' => now()->subDays(4)],
            ['type' => 'closed_not_resolved', 'description' => 'Chiusura non risolta', 'responsible' => 'Supporto', 'performed_at' => now()->subDays(1)],
        ];
        foreach ($scenarios as $s) {
            TimelineStep::firstOrCreate(['type' => $s['type'], 'description' => $s['description']], $s);
        }
    }
}

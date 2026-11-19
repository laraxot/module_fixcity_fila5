<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\preg_match_all;

uses(TestCase::class);

/*
 * UX-07: chiavi usate da notifiche, azioni del back office, KPI e wizard che mancavano in de ed es.
 * Il fallback e' l'italiano: senza questo test una chiave persa non da' errore, mostra solo italiano.
 * Solo filesystem, nessun database. Funzioni come closure per non dichiarare helper globali.
 */
$moduleLang = dirname(__DIR__, 3).'/lang';

$requiredKeys = [
    'create_ticket_wizard.fields.address.label',
    'create_ticket_wizard.fields.details.label',
    'create_ticket_wizard.fields.issueType.label',
    'create_ticket_wizard.fields.title.label',
    'create_ticket_wizard.summary.empty',
    'fixcity.ticket.actions.next.label',
    'fixcity.ticket.actions.save.label',
    'fixcity.ticket.fields.accept_terms.helper',
    'fixcity.ticket.fields.accept_terms.label',
    'fixcity.ticket.fields.issue.label',
    'fixcity.ticket.steps.auth.description',
    'fixcity.ticket.steps.data.description',
    'fixcity.ticket.validation.accept_terms',
    'segnalazione.steps.current_of_total.label',
    'ticket.actions.approve.label',
    'ticket.actions.approve.reason_label',
    'ticket.actions.assign.label',
    'ticket.actions.changeStatus.modal_heading',
    'ticket.actions.changeStatus.modal_submit',
    'ticket.actions.changeStatus.reason_help',
    'ticket.actions.changeStatus.reason_label',
    'ticket.actions.changeStatus.tooltip',
    'ticket.actions.close.label',
    'ticket.actions.close.reason_label',
    'ticket.actions.record_inbound.label',
    'ticket.actions.record_inbound.sender',
    'ticket.actions.record_inbound.summary',
    'ticket.actions.record_inbound.summary_help',
    'ticket.actions.reject.label',
    'ticket.actions.reject.reason_label',
    'ticket.actions.resolve.label',
    'ticket.actions.resolve.reason_label',
    'ticket.actions.send_communication.assignee_fallback',
    'ticket.actions.send_communication.body',
    'ticket.actions.send_communication.label',
    'ticket.actions.send_communication.office_fallback',
    'ticket.actions.send_communication.recipient_email',
    'ticket.actions.send_communication.recipient_help',
    'ticket.actions.send_communication.recipient_label',
    'ticket.actions.send_communication.subject',
    'ticket.actions.send_followup.label',
    'ticket.actions.send_followup.office',
    'ticket.contacts.block_title.label',
    'ticket.fields.address.label',
    'ticket.fields.content.label',
    'ticket.fields.email.label',
    'ticket.fields.images.label',
    'ticket.fields.name.label',
    'ticket.fields.responsible_id.label',
    'ticket.fields.title.label',
    'ticket.fields.type.label',
    'ticket.filters.legend.label',
    'ticket.filters.status.legend.label',
    'ticket.heading.subtitle.text',
    'ticket.heading.title.label',
    'ticket.map.cta.button.label',
    'ticket.map.cta.text.label',
    'ticket.map.cta.title.label',
    'ticket.map.image.alt',
    'ticket.notification.assigned_to_you',
    'ticket.notification.assignment_title',
    'ticket.notification.status_changed',
    'ticket.notification.title',
    'ticket.privacy.not_configured',
    'ticket.privacy.open_notice',
    'ticket.sections.author.label',
    'ticket.sections.contacts.edit_action',
    'ticket.sections.summary.label',
    'ticket.subscription.discover_tickets',
    'ticket.subscription.follow',
    'ticket.subscription.followed_count',
    'ticket.subscription.followed_description',
    'ticket.subscription.followed_empty',
    'ticket.subscription.followed_title',
    'ticket.subscription.following_status',
    'ticket.subscription.heading',
    'ticket.subscription.login_to_follow',
    'ticket.subscription.not_following_status',
    'ticket.subscription.personal_area_navigation',
    'ticket.subscription.tracking_unavailable',
    'ticket.subscription.unfollow',
    'ticket.warning.summary_declaration.text',
    'ticket.warning.title.label',
    'ticket_filter.assignment_status.assigned',
    'ticket_filter.assignment_status.unassigned',
    'ticket_kpi.export.header_action.label',
    'ticket_kpi.sla.avg_hours.description.label',
    'ticket_kpi.sla.avg_hours.label',
    'ticket_kpi.sla.resolved_30d.description.label',
    'ticket_kpi.sla.resolved_30d.label',
    'ticket_kpi.sla.resolved_total.description.label',
    'ticket_kpi.sla.resolved_total.label',
    'ticket_kpi.stats.backlog.description.label',
    'ticket_kpi.stats.backlog.label',
    'ticket_kpi.stats.in_progress.description.label',
    'ticket_kpi.stats.in_progress.label',
    'ticket_kpi.stats.resolved.description.label',
    'ticket_kpi.stats.resolved.label',
    'ticket_kpi.stats.total.description.label',
    'ticket_kpi.stats.total.label',
];

$readLeaf = static function (string $langDir, string $locale, string $key): ?string {
    $parts = explode('.', $key);
    $file = $langDir.'/'.$locale.'/'.array_shift($parts).'.php';
    if (! is_file($file)) {
        return null;
    }
    $node = require $file;
    foreach ($parts as $part) {
        if (! is_array($node) || ! array_key_exists($part, $node)) {
            return null;
        }
        $node = $node[$part];
    }

    return is_string($node) ? $node : null;
};

$placeholders = static function (string $value): array {
    preg_match_all('/:[a-z_]+/', $value, $matches);
    $found = $matches[0];
    sort($found);

    return $found;
};

it('translates the ticket workflow, notification and KPI keys in de and es', function () use ($moduleLang, $requiredKeys, $readLeaf, $placeholders): void {
    foreach ($requiredKeys as $key) {
        $italian = $readLeaf($moduleLang, 'it', $key);
        Assert::assertNotNull($italian, 'it: '.$key);

        foreach (['de', 'es'] as $locale) {
            $translated = $readLeaf($moduleLang, $locale, $key);
            Assert::assertNotNull($translated, $locale.': missing '.$key);
            Assert::assertNotSame('', trim($translated), $locale.': empty '.$key);
            Assert::assertSame($placeholders($italian), $placeholders($translated), $locale.': placeholders differ in '.$key);
        }
    }
});

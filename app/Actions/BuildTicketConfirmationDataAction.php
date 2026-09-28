<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Session;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Data bag CMS per la pagina di conferma post-create (email, receipt, area riservata).
 */
final class BuildTicketConfirmationDataAction
{
    use QueueableAction;

    public const string SESSION_KEY = 'fixcity.ticket_confirmation';

    /**
     * @return array{
     *     code: string,
     *     slug: string,
     *     status: string,
     *     email: string,
     *     summary_html: string,
     *     visibility_html: string,
     *     email_intro_html: string,
     *     receipt: array{url: string, label: string, icon: string},
     *     reserved_area: array{link_label: string, text: string, url: string}
     * }
     */
    public function execute(Ticket $ticket, ?string $locale = null): array
    {
        $locale ??= LaravelLocalization::getCurrentLocale();
        $code = SafeStringCastAction::cast($ticket->code);
        $slug = SafeStringCastAction::cast($ticket->slug);
        $status = $ticket->resolveTicketStatusValue();
        $trackPath = $code !== '' ? '/tickets/track/'.$code : '/tickets/track';
        $trackUrl = $this->localize($trackPath, $locale);
        $praticheUrl = $this->localize('/area-personale/pratiche', $locale);

        $email = '';
        $owner = $ticket->owner;
        if (is_object($owner) && isset($owner->email) && is_string($owner->email)) {
            $email = $owner->email;
        }

        $summary = $code !== ''
            ? (string) trans('fixcity::ticket_confirmation.summary.with_code', ['code' => e($code)], $locale)
            : (string) trans('fixcity::ticket_confirmation.summary.without_code', [], $locale);
        $visibility = (string) trans('fixcity::ticket_confirmation.visibility', [
            'url' => e($this->localize('/tickets', $locale)),
        ], $locale);

        return [
            'code' => $code,
            'slug' => $slug,
            'status' => $status,
            'email' => $email,
            'summary_html' => $summary,
            'visibility_html' => $visibility,
            'email_intro_html' => (string) trans('fixcity::ticket_confirmation.email_intro', [], $locale),
            'receipt' => [
                'url' => $trackUrl,
                'label' => $code !== ''
                    ? (string) trans('fixcity::ticket_confirmation.receipt.with_code', ['code' => $code], $locale)
                    : (string) trans('fixcity::ticket_confirmation.receipt.without_code', [], $locale),
                'icon' => 'it-search',
            ],
            'reserved_area' => [
                'link_label' => (string) trans('fixcity::ticket_confirmation.reserved_area.link_label', [], $locale),
                'text' => (string) trans('fixcity::ticket_confirmation.reserved_area.text', [], $locale),
                'url' => $praticheUrl,
            ],
        ];
    }

    public function flash(Ticket $ticket, ?string $locale = null): void
    {
        Session::flash(self::SESSION_KEY, $this->execute($ticket, $locale));
    }

    /**
     * @return array<string, mixed>
     */
    public function pullFromSession(): array
    {
        $payload = Session::pull(self::SESSION_KEY, []);
        if (! is_array($payload)) {
            return [];
        }

        $typed = [];
        foreach ($payload as $key => $value) {
            if (is_string($key)) {
                $typed[$key] = $value;
            }
        }

        return $typed;
    }

    private function localize(string $path, string $locale): string
    {
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        if (! array_key_exists($locale, $supportedLocales)) {
            $locale = LaravelLocalization::getCurrentLocale();
        }

        $localized = LaravelLocalization::getLocalizedURL($locale, $path);

        return $localized !== false ? $localized : $path;
    }
}

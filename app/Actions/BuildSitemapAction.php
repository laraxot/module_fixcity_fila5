<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Http\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Cms\Models\Page;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

use function Safe\preg_replace;

/**
 * sitemap.xml delle pagine pubbliche in tutte le lingue (Linee guida di design 4.3.9, DCP-166).
 *
 * Le pagine vengono dal CMS (JSON del tenant). Restano fuori: le pagine di prova (`tests.`), quelle dietro `auth`
 * (non indicizzabili) e quelle che hanno senso solo con un parametro (`tickets.confirmation` usa `?code`, un dato personale;
 * `segnalazione-dettaglio` e' uno schema). Niente `lastmod`: non c'e' una data affidabile per pagina (DCP-167).
 */
final class BuildSitemapAction
{
    use QueueableAction;

    /**
     * `segnalazioni.index` e' l'alias Design Comuni di `tickets` (301 a /{lang}/tickets, FrontofficeContainerAliases del tema):
     * in sitemap va solo l'URL finale, un reindirizzamento e' un «URL non seguito» per Search Console (DCP-272).
     *
     * @var list<string>
     */
    private const array NOT_INDEXABLE = ['tickets.confirmation', 'segnalazione-dettaglio', 'segnalazioni.index'];

    /** Risposta HTTP della rotta `/sitemap.xml` (invokable: `route:cache` non ammette closure). */
    public function __invoke(): Response
    {
        return response($this->execute(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function execute(): string
    {
        $locales = array_keys(LaravelLocalization::getSupportedLocales());
        $default = LaravelLocalization::getDefaultLocale();
        $default = is_string($default) ? $default : 'it';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($this->indexablePaths() as $path) {
            foreach ($locales as $locale) {
                $xml .= "  <url>\n    <loc>".$this->url($locale, $path)."</loc>\n";
                foreach ($locales as $alternate) {
                    $xml .= '    <xhtml:link rel="alternate" hreflang="'.$alternate.'" href="'.$this->url($alternate, $path)."\"/>\n";
                }
                $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.$this->url($default, $path)."\"/>\n  </url>\n";
            }
        }

        return $xml.'</urlset>'."\n";
    }

    /**
     * @return list<string> percorsi senza lingua: '' per la home, 'privacy', 'tickets/track'
     */
    private function indexablePaths(): array
    {
        $paths = [];
        foreach (Page::query()->get() as $page) {
            $slug = app(SafeStringCastAction::class)->execute($page->getAttribute('slug'));
            $middleware = $page->getAttribute('middleware');
            // `auth.login` e `auth.register` sono pagine per ospiti (senza middleware `auth`) ma robots.txt chiude /{lang}/auth.
            if ($slug === '' || str_starts_with($slug, 'tests.') || str_starts_with($slug, 'auth.') || in_array($slug, self::NOT_INDEXABLE, true)) {
                continue;
            }
            if (is_array($middleware) && in_array('auth', $middleware, true)) {
                continue;
            }
            $paths[] = $this->pathOf($slug);
        }
        sort($paths);

        return array_values(array_unique($paths));
    }

    private function pathOf(string $slug): string
    {
        $slug = preg_replace('/\.index$/', '', $slug);

        return $slug === 'home' ? '' : str_replace('.', '/', $slug);
    }

    private function url(string $locale, string $path): string
    {
        return htmlspecialchars(url('/'.$locale.($path === '' ? '' : '/'.$path)), ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}

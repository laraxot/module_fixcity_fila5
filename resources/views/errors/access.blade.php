{{--
    419 (sessione scaduta) e 403 (accesso negato) del front office con il layout del tema: prima il cittadino vedeva la pagina nuda di Laravel in inglese
    ("Page Expired", "Forbidden"). Il gestore in bootstrap/app.php passa `$kind` (`expired` | `forbidden`) e `$status`; nessun dettaglio tecnico e nessun
    messaggio dell'eccezione. Solo link che funzionano: accesso e home, nella lingua della richiesta. Stessa struttura di errors/not-found.blade.php.
--}}
@php
    $locale = app()->getLocale();
@endphp
<x-layouts.app :title="__('fixcity::error_pages.'.$kind.'.title')">
    {{-- `data-dc` accende lo scope Design Comuni: fuori da li' il CSS legacy toglie il bordo a ogni `.btn` --}}
    <div class="page-content content" data-dc>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7">
                <section class="card shadow-sm border-0" aria-labelledby="access-error-heading">
                    <div class="card-body p-4 p-md-5 text-center">
                        <p class="text-muted mb-2">{{ $status }}</p>
                        <h1 id="access-error-heading" class="h2 mb-3">{{ __('fixcity::error_pages.'.$kind.'.title') }}</h1>
                        <p class="mb-4">{{ __('fixcity::error_pages.'.$kind.'.message') }}</p>
                        <div class="d-flex flex-column flex-md-row justify-content-center gap-2">
                            <a class="btn btn-primary" href="{{ url('/'.$locale.'/auth/login') }}">{{ __('fixcity::error_pages.login') }}</a>
                            <a class="btn btn-outline-primary" href="{{ url('/'.$locale) }}">{{ __('fixcity::error_pages.home') }}</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    </div>
</x-layouts.app>

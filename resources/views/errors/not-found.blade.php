{{--
    404 del front office con il layout del tema (DCP-151). Il gestore in bootstrap/app.php la usa per le richieste HTML di un cittadino (non per API,
    Livewire, back office); se il rendering fallisce ricade sulla pagina standard di Laravel, quindi un errore del layout non diventa un 500.
    Solo link che funzionano: home, elenco, nuova segnalazione, nella lingua della richiesta.
--}}
@php
    $locale = app()->getLocale();
@endphp
<x-layouts.app :title="__('fixcity::error_pages.not_found.title')">
    {{-- `data-dc` accende lo scope Design Comuni: fuori da li' il CSS legacy toglie il bordo a ogni `.btn` e i pulsanti secondari sembrerebbero link --}}
    <div class="page-content content" data-dc>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7">
                <section class="card shadow-sm border-0" aria-labelledby="not-found-heading">
                    <div class="card-body p-4 p-md-5 text-center">
                        <p class="text-muted mb-2">404</p>
                        <h1 id="not-found-heading" class="h2 mb-3">{{ __('fixcity::error_pages.not_found.title') }}</h1>
                        <p class="mb-4">{{ __('fixcity::error_pages.not_found.message') }}</p>
                        <div class="d-flex flex-column flex-md-row justify-content-center gap-2">
                            <a class="btn btn-primary" href="{{ url('/'.$locale) }}">{{ __('fixcity::error_pages.not_found.home') }}</a>
                            <a class="btn btn-outline-primary" href="{{ url('/'.$locale.'/tickets') }}">{{ __('fixcity::error_pages.not_found.list') }}</a>
                            <a class="btn btn-outline-primary" href="{{ url('/'.$locale.'/tickets/create') }}">{{ __('fixcity::error_pages.not_found.create') }}</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    </div>
</x-layouts.app>

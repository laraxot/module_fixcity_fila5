<x-layouts.app :title="__('fixcity::ticket.track.rate_limited_title')">
    <main id="main-container" class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7">
                <section class="card shadow-sm border-0" aria-labelledby="rate-limit-heading">
                    <div class="card-body p-4 p-md-5 text-center">
                        <p class="text-muted mb-2">429</p>
                        <h1 id="rate-limit-heading" class="h2 mb-3">
                            {{ __('fixcity::ticket.track.rate_limited_title') }}
                        </h1>
                        <p class="mb-4" role="status">
                            {{ __('fixcity::ticket.track.rate_limited_message') }}
                        </p>
                        <a class="btn btn-primary" href="{{ url()->current() }}">
                            {{ __('fixcity::ticket.track.rate_limited_retry') }}
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </main>
</x-layouts.app>

<x-layouts.app :title="__('fixcity::privacy.page_title')">
    <main id="main-container" class="container py-4 py-lg-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9">
                @if (is_string($policyHtml))
                    <article class="card shadow-sm border-0" aria-labelledby="privacy-policy-heading">
                        <div class="card-body p-4 p-md-5 prose">
                            <h1 id="privacy-policy-heading" class="h2">
                                {{ __('fixcity::privacy.page_title') }}
                            </h1>
                            {!! $policyHtml !!}
                        </div>
                    </article>
                @else
                    <section class="alert alert-warning" role="alert" aria-labelledby="privacy-policy-heading">
                        <h1 id="privacy-policy-heading" class="h2">
                            {{ __('fixcity::privacy.page_title') }}
                        </h1>
                        <p class="mb-0">{{ __('fixcity::privacy.not_configured') }}</p>
                    </section>
                @endif
            </div>
        </div>
    </main>
</x-layouts.app>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="it-hero-wrapper it-dark it-overlay">
                <!-- Image -->
                <div class="img-responsive-wrapper">
                    <div class="img-responsive">
                        <div class="img-wrapper">
                            <img src="{{ Theme::asset('pub_theme::img/city-login.jpg') }}" alt="Login banner">
                        </div>
                    </div>
                </div>
                <!-- Content -->
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="it-hero-text-wrapper bg-dark px-4 py-5">
                                <h1 class="text-center mb-4">
                                    Accedi a FixCity
                                </h1>
                                <form wire:submit.prevent="authenticate" class="needs-validation" novalidate>
                                    <div class="form-group mb-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="it-mail"></i>
                                                </div>
                                            </div>
                                            <input type="email" 
                                                class="form-control @error('email') is-invalid @enderror" 
                                                wire:model.defer="email"
                                                placeholder="Email"
                                                required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="it-password"></i>
                                                </div>
                                            </div>
                                            <input type="password" 
                                                class="form-control @error('password') is-invalid @enderror" 
                                                wire:model.defer="password"
                                                placeholder="Password"
                                                required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-check mb-4">
                                        <input class="form-check-input" 
                                            type="checkbox" 
                                            wire:model.defer="remember"
                                            id="remember">
                                        <label class="form-check-label" for="remember">
                                            Ricordami
                                        </label>
                                    </div>

                                    @if (session()->has('error'))
                                        <div class="alert alert-danger mb-4">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <div class="d-grid gap-2">
                                        <button type="submit" 
                                            class="btn btn-primary btn-lg"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove>Accedi</span>
                                            <span wire:loading>
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Accesso in corso...
                                            </span>
                                        </button>

                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="btn btn-outline-primary">
                                                Password dimenticata?
                                            </a>
                                        @endif
                                    </div>

                                    <div class="text-center mt-4">
                                        <p class="mb-0">Non hai un account?</p>
                                        <a href="{{ route('register') }}" class="btn btn-link">
                                            Registrati
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.it-hero-wrapper {
    border-radius: 1rem;
    overflow: hidden;
}

.it-hero-text-wrapper {
    border-radius: 0 0 1rem 1rem;
}

.input-group-text {
    background-color: transparent;
    border-right: 0;
}

.form-control {
    border-left: 0;
}

.input-group:focus-within {
    box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
}

.btn-primary {
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}
</style>
@endpush 
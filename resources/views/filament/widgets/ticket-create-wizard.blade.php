@page

@extends('pub_theme::layouts.app')

@section('content')

<div class="fi-sc-wizard">
    @include('filament.wizard::steps')

    <div class="fi-sc-wizard-footer">
        <div class="fi-sc-wizard-actions">
            @if(!isFirstStep())
                <x-pub_theme::wizard.button action="previous" class="fi-b fi-b-secondary">Anullare</x-pub_theme::wizard.button>
            @endif

            <div x-show="!isLastStep()">
                <x-pub_theme::wizard.button action="next" class="fi-b fi-b-primary">Prossimo passo</x-pub_theme::wizard.button>
            </div>

            @if(isLastStep())
                <x-pub_theme::wizard.button action="submit" class="fi-b fi-b-success">Invia</x-pub_theme::wizard.button>
            @endif
        </div>
    </div>

</div>

@endsection

@section('js')
    <script>
        // Wizard initialization logic
        // (Add Livewire event handlers if needed)
    </script>
@endsection
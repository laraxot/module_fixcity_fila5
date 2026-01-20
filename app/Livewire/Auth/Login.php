<?php

namespace Modules\Fixcity\Livewire\Auth;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public ?array $data = [];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->label(__('Email'))
                    ->placeholder(__('Inserisci la tua email'))
                    ->autofocus(),

                TextInput::make('password')
                    ->password()
                    ->required()
                    ->label(__('Password'))
                    ->placeholder(__('Inserisci la tua password')),

                Checkbox::make('remember')
                    ->label(__('Ricordami')),
            ])
            ->statePath('data');
    }

    public function authenticate()
    {
        $validated = $this->form->getState();

        if (Auth::attempt($validated)) {
            session()->regenerate();

            Notification::make()
                ->title(__('Login effettuato con successo'))
                ->success()
                ->send();

            return redirect()->intended();
        }

        $this->addError('email', __('Le credenziali fornite non sono corrette.'));

        Notification::make()
            ->title(__('Le credenziali fornite non sono corrette'))
            ->danger()
            ->send();
    }

    public function render()
    {
        return view('pub_theme::livewire.auth.login');
    }
}

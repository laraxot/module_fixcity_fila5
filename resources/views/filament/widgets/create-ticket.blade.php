<div>
<x-filament-widgets::widget>
    
    <form wire:submit="create">
        {{ $this->form }}
        <style>
            .fi-btn.fi-btn-color-primary {
                background-color: #007a52 !important;
                color: white !important;
            }
            
            .fi-btn.fi-btn-color-primary:hover {
                background-color: #007a52 !important;
            }
            .fi-btn.fi-color-custom {
                @apply bg-emerald-600 hover:bg-emerald-500;
            }

            .subtitle {
                font-family: 'Titillium Web', sans-serif !important;
                font-weight: 800 !important;
            }

            /* Style submit button */
            button[type="submit"].fi-btn {
                background-color: white !important;
                color: #007a52 !important;
                border: 2px solid #007a52 !important;
                --c-400: none !important;
                --c-500: none !important;
                --c-600: none !important;
                cursor: pointer;
            }

            button[type="submit"].fi-btn:hover {
                background-color: white !important;
                color: #007a52 !important;
                border: 2px solid #007a52 !important;
                --c-400: none !important;
                --c-500: none !important;
                --c-600: none !important;
                cursor: pointer;
            }

            .fi-fo-wizard-header-step-indicator {
                display: none !important;
            }

            /* Hide the circle container */
            .fi-fo-wizard-header-step-icon-ctn {
                display: none !important;
            }

            /* Modify step borders */
            .fi-fo-wizard-header {
                border: none !important;
            }

            .fi-fo-wizard-header-step {
                border-left: 2px solid #e5e7eb !important;
                border-bottom: none !important;
            }

            .fi-fo-wizard-header-step.fi-active {
                border-left: 2px solid #007a52 !important;
                border-bottom: 2px solid #007a52 !important;
            }

            /* Change text color and font for active step */
            .fi-fo-wizard-header-step.fi-active .fi-fo-wizard-header-step-label {
                color: #007a52 !important;
                font-size: 1.125rem !important;
                font-weight: 600 !important;
            }

            /* Style for all step labels */
            .fi-fo-wizard-header-step-label {
                font-size: 1.125rem !important;
                font-weight: 600 !important;
            }

            /* Hide the separator between steps */
            .fi-fo-wizard-header-step-separator {
                display: none !important;
            }

            /* Style error message */
            .fi-fo-field-wrp-error-message {
                color: #dc2626 !important;
                font-weight: 500 !important;
                font-size: 1rem !important;
                margin-top: 0.5rem !important;
            }

            /* Add grey background to outer wrapper and padding */
            .fi-section-content-ctn {
                background-color: lightgrey !important;
                padding: 1.5rem !important;
                border-radius: 0.5rem !important;
            }

            /* Keep inner content section white */
            .fi-section-content {
                background-color: white !important;
                border-radius: 0.5rem !important;
                padding: 1.5rem !important;
            }

            /* Remove default section styling */
            .fi-section {
                background-color: transparent !important;
                box-shadow: none !important;
                border: none !important;
            }

            /* Style previous step button */
            button[type="button"].fi-btn-color-gray {
                background-color: white !important;
                color: #007a52 !important;
                --c-400: none !important;
                --c-500: none !important;
                --c-600: none !important;
                border: none !important;
                cursor: pointer;
            }

            button[type="button"].fi-btn-color-gray:hover {
                background-color: white !important;
                color: #007a52 !important;
                --c-400: none !important;
                --c-500: none !important;
                --c-600: none !important;
                border: none !important;
                cursor: pointer;
            }
        </style>

    </form>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
</div>
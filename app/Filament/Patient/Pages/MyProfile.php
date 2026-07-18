<?php

namespace App\Filament\Patient\Pages;

use App\Models\Patient;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MyProfile extends Page implements HasForms
{
    use InteractsWithForms;

    // Page setup
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.patient.pages.my-profile';
    protected static ?string $title = 'My Profile';
    protected static ?string $slug = 'my-profile';

    // State properties for the forms
    public ?array $personalInformationData = [];
    public ?array $passwordData = [];

    /**
     * Mount the component and fill the forms with initial data.
     */
    public function mount(): void
    {
        // Pre-fill the personal information form with the logged-in patient's data
        $this->personalInformationForm->fill(
            auth()->user()->patient->attributesToArray()
        );

        // Ensure the password form is empty on page load
        $this->passwordForm->fill();
    }

    /**
     * Defines the form for updating personal information.
     */
    public function personalInformationForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->description('Manage and update your personal details.')
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->disabled()
                            ->dehydrated(false), // Do not include in the update
                        TextInput::make('last_name')
                            ->required()
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->disabled()
                            ->dehydrated(false),
                        DatePicker::make('date_of_birth')
                            ->required()
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('mobile_number')
                            ->tel()
                            ->required(),
                        Textarea::make('address')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('hospital_name')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2)
            ])
            ->statePath('personalInformationData')
            ->model(auth()->user()->patient); // Link form to the patient model
    }

    /**
     * Defines the form for changing the user's password.
     */
    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Change Password')
                    ->description('Update your password for enhanced security.')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->required()
                            ->currentPassword(), // Built-in Filament rule to check password
                        TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->required()
                            ->rule(Password::default()->min(8))
                            ->confirmed(), // Ensures it matches the confirmation field
                        TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->required(),
                    ])
            ])
            ->statePath('passwordData');
    }

    /**
     * Save action for the personal information form.
     */
    public function savePersonalInformation(): void
    {
        $patient = auth()->user()->patient;
        $data = $this->personalInformationForm->getState();

        $patient->update($data);

        Notification::make()
            ->title('Personal information updated successfully!')
            ->success()
            ->send();
    }

    /**
     * Save action for the change password form.
     */
    public function changePassword(): void
    {
        $data = $this->passwordForm->getState();
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);
        
        // This is a good security practice to re-hash the session password
        if (request()->hasSession()) {
            request()->session()->put([
                'password_hash_' . auth()->getDefaultDriver() => $user->getAuthPassword(),
            ]);
        }

        // Reset the form fields after a successful update
        $this->passwordForm->fill(); 

        Notification::make()
            ->title('Password changed successfully!')
            ->success()
            ->send();
    }

    /**
     * This method is required by the HasForms trait to know about the forms.
     */
    protected function getForms(): array
    {
        return [
            'personalInformationForm',
            'passwordForm',
        ];
    }
}
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonorResource\Pages;
use App\Filament\Resources\DonorResource\RelationManagers;
use App\Models\Donor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use App\Mail\DonorAccountApproved;
use Illuminate\Support\Facades\Mail;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DonorResource extends Resource
{
    protected static ?string $model = Donor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('mobile_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ])
                            ->required(),
                        DatePicker::make('date_of_birth')
                            ->required()
                            ->maxDate(now()),
                        Textarea::make('address')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Group::make()
                    ->schema([
                        Select::make('state_id')
                            ->relationship('state', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('city_id')
                            ->relationship('city', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        Select::make('blood_group_id')
                            ->relationship('bloodGroup', 'group_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        DatePicker::make('last_donation_date')
                            ->nullable()
                            ->maxDate(now()),
                        DatePicker::make('eligible_to_donate_until')
                            ->nullable()
                            ->minDate(now()),
                        TextInput::make('enrollment_number')
                            ->nullable()
                            ->maxLength(255),
                        Select::make('status')
                            ->options([
                                'pending_verification' => 'Pending Verification',
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->required()
                            ->default('pending_verification'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('User Account Information')
                    ->schema([
                        TextInput::make('user_name')
                            ->label('User Name')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->hiddenOn('edit')
                            ->dehydrateStateUsing(fn (?string $state) => $state ?? (fn (callable $get) => $get('first_name') . ' ' . $get('last_name'))),
                        TextInput::make('user_email')
                            ->label('User Email')
                            ->email()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            // ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            // ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->hiddenOn('edit'),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->requiredWith('password')
                            ->maxLength(255)
                            ->hiddenOn('edit')
                            ->same('password'),
                    ])
                    ->columns(2)
                    ->visible(fn (string $operation): bool => $operation === 'create' || $form->getRecord()->user === null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bloodGroup.group_name')
                    ->label('Blood Group')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city.name')
                    ->label('City')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending_verification' => 'warning',
                        'active' => 'success',
                        'inactive' => 'danger',
                        'suspended' => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('blood_group')
                    ->relationship('bloodGroup', 'group_name')
                    ->label('Blood Group'),
                SelectFilter::make('city')
                    ->relationship('city', 'name')
                    ->label('City'),
                SelectFilter::make('status')
                    ->options([
                        'pending_verification' => 'Pending Verification',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ])
                    ->label('Status'),
                TernaryFilter::make('pending_verification')
                    ->label('Pending Verification')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->where('status', 'pending_verification'),
                        false: fn (Builder $query): Builder => $query->where('status', '!=', 'pending_verification'),
                        blank: fn (Builder $query): Builder => $query,
                    ),
            ])
            ->actions([
                Action::make('approve')
                    ->action(function (Donor $record) {
                        $record->status = 'active';
                        $record->save();
                        Mail::to($record->user->email)->send(new DonorAccountApproved($record));
                    })
                    ->label('Approve')
                    ->visible(fn (Donor $record): bool => $record->status === 'pending_verification')
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
                Action::make('suspend')
                    ->action(function (Donor $record) {
                        $record->status = 'suspended';
                        $record->save();
                    })
                    ->label('Suspend')
                    ->visible(fn (Donor $record): bool => $record->status !== 'suspended')
                    ->color('warning')
                    ->icon('heroicon-o-pause-circle'),
                Action::make('deactivate')
                    ->action(function (Donor $record) {
                        $record->status = 'inactive';
                        $record->save();
                    })
                    ->label('Deactivate')
                    ->visible(fn (Donor $record): bool => $record->status !== 'inactive')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle'),
                Tables\Actions\ViewAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_all_csv')
                    ->label('Export All CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $filename = 'donors_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
                        $filepath = storage_path('app/public/' . $filename);
    
                        $handle = fopen($filepath, 'w');
    
                        // CSV headers
                        fputcsv($handle, [
                            'Name',
                            'Email',
                            'Mobile Number',
                            'Gender',
                            'Blood Group',
                            'City',
                            'State',
                            'Last Donation Date',
                            'Eligible To Donate Until',
                            'Status',
                            'Created At',
                        ]);
    
                        // Fetch all donors
                        $donors = \App\Models\Donor::with(['user', 'bloodGroup', 'city', 'state'])->get();
    
                        foreach ($donors as $donor) {
                            fputcsv($handle, [
                                $donor->user?->name,
                                $donor->user?->email,
                                $donor->mobile_number,
                                ucfirst($donor->gender),
                                $donor->bloodGroup?->group_name,
                                $donor->city?->name,
                                $donor->state?->name,
                                optional($donor->last_donation_date)->format('Y-m-d'),
                                optional($donor->eligible_to_donate_until)->format('Y-m-d'),
                                ucfirst($donor->status),
                                optional($donor->created_at)->format('Y-m-d H:i:s'),
                            ]);
                        }
    
                        fclose($handle);
    
                        return response()->download($filepath)->deleteFileAfterSend(true);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonors::route('/'),
            'create' => Pages\CreateDonor::route('/create'),
            'edit' => Pages\EditDonor::route('/{record}/edit'),
            'view' => Pages\ViewDonor::route('/{record}'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::create([
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['user_email'],
            'password' => $data['password'],
            'role' => 'donor',
        ]);

        $data['user_id'] = $user->id;
        unset($data['user_name']);
        unset($data['user_email']);
        unset($data['password']);
        unset($data['password_confirmation']);

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data, ?Donor $record = null): array
    {
        if ($record && $record->user) {
            $record->user->update([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['user_email'] ?? $record->user->email,
            ]);
        } 
        
        unset($data['user_name']);
        unset($data['user_email']);
        unset($data['password']);
        unset($data['password_confirmation']);

        return $data;
    }
}

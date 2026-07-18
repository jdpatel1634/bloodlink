<?php

namespace App\Filament\Patient\Resources;

use App\Filament\Patient\Resources\BloodRequestResource\Pages;
use App\Models\BloodRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BloodRequestResource extends Resource
{
    protected static ?string $model = BloodRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'My Blood Requests';
    protected static ?string $pluralLabel = 'Blood Requests';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('request_date')
                    ->label('Request Date')
                    ->default(now())
                    ->disabled()
                    ->columnSpanFull(),

                Forms\Components\Select::make('blood_group_id')
                    ->relationship('bloodGroup', 'group_name')
                    ->label('Blood Group')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('units_requested')
                    ->label('Units Requested')
                    ->numeric()
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('urgency_level')
                    ->options([
                        'routine' => 'Routine',
                        'urgent' => 'Urgent',
                        'emergency' => 'Emergency',
                    ])
                    ->label('Urgency Level')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('required_by_date')
                    ->label('Required By Date')
                    ->native(false)
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'fulfilled' => 'Fulfilled',
                        'rejected' => 'Rejected',
                        'canceled' => 'Canceled',
                    ])
                    ->label('Status')
                    ->disabled()
                    ->default('pending')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_date')
                    ->date()
                    ->label('Request Date')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bloodGroup.group_name')
                    ->label('Blood Group')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('units_requested')
                    ->label('Units')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'fulfilled' => 'success',
                        'rejected' => 'danger',
                        'canceled' => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->visible(fn (?BloodRequest $record): bool => $record && $record->status == 'rejected')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (?BloodRequest $record): bool => $record && $record->status == 'pending')
                    ->requiresConfirmation()
                    ->action(function (BloodRequest $record) {
                        $record->update(['status' => 'canceled']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        // Only show logged-in patient's requests
        return parent::getEloquentQuery()
            ->where('patient_id', auth()->user()->patient->id)
            ->latest();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBloodRequests::route('/'),
            'create' => Pages\CreateBloodRequest::route('/create'),
            'edit' => Pages\EditBloodRequest::route('/{record}/edit'),
        ];
    }
}

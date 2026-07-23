<?php

namespace App\Filament\Resources\BloodUnitResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SerologyTestsRelationManager extends RelationManager
{
    protected static string $relationship = 'serologyTests';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('test_type')
                    ->label('Test Type')
                    ->options([
                        'HIV' => 'HIV',
                        'Hepatitis B' => 'Hepatitis B',
                        'Hepatitis C' => 'Hepatitis C',
                        'Syphilis' => 'Syphilis',
                    ])
                    ->required()
                    ->columnSpanFull(),
                Select::make('result')
                    ->label('Result')
                    ->options([
                        'positive' => 'Positive',
                        'negative' => 'Negative',
                        'indeterminate' => 'Indeterminate',
                    ])
                    ->required()
                    ->columnSpanFull(),
                DatePicker::make('test_date')
                    ->label('Test Date')
                    ->required()
                    ->default(now())
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Notes')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('test_type')
            ->columns([
                Tables\Columns\TextColumn::make('test_type'),
                Tables\Columns\TextColumn::make('result')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'positive' => 'danger',
                        'negative' => 'success',
                        'indeterminate' => 'warning',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('test_date')
                    ->date(),
                Tables\Columns\TextColumn::make('testedBy.name')
                    ->label('Tested By'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['tested_by_user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

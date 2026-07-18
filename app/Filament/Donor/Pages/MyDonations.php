<?php

namespace App\Filament\Donor\Pages;

use Filament\Pages\Page;
use App\Models\BloodUnit;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyDonations extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.donor.pages.my-donations';

    protected static ?string $title = 'My Donations';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BloodUnit::query()
                    ->whereHas('donor.user', function (Builder $query) {
                        $query->where('id', Auth::id());
                    })
                    ->with('collectionCamp')
            )
            ->columns([
                TextColumn::make('collection_date')
                    ->date()
                    ->label('Donation Date'),
                TextColumn::make('collectionCamp.name')
                    ->default('Main Blood Bank')
                    ->label('Location'),
                TextColumn::make('component_type')
                    ->label('Component Type'),
                TextColumn::make('unique_bag_id')
                    ->label('Bag ID'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'collected' => 'gray',
                        'test_awaited' => 'warning',
                        'tested' => 'info',
                        'ready_for_issue' => 'success',
                        'issued' => 'success',
                        'expired' => 'danger',
                        'discarded' => 'danger',
                        'quarantined' => 'danger',
                    }),
            ]);
    }
}

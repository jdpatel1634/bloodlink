<?php

namespace App\Filament\Resources\BloodIssueResource\Pages;

use App\Filament\Resources\BloodIssueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBloodIssue extends EditRecord
{
    protected static string $resource = BloodIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

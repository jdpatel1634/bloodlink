<?php

namespace App\Filament\Patient\Widgets;

use App\Models\BloodRequest;
use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PatientDashboardOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $user = Auth::user();
        $patient = $user->patient;

        if (!$patient) {
            return [];
        }

        $activeRequestsCount = $patient->bloodRequests()
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        $totalRequestsCount = $patient->bloodRequests()->count();

        $lastRequest = $patient->bloodRequests()
            ->latest('request_date')
            ->first();

        $lastRequestStatus = 'N/A';
        if ($lastRequest) {
            $lastRequestStatus = 'Your request from ' . $lastRequest->request_date->format('M d, Y') . ' is currently ' . ucfirst($lastRequest->status) . '.';
        }

        return [
            Stat::make('Welcome', 'Welcome, ' . $patient->first_name . '!'),
            Stat::make('Active Requests', $activeRequestsCount),
            Stat::make('Total Requests', $totalRequestsCount),
            Stat::make('Last Request Status', $lastRequestStatus),
        ];
    }
}

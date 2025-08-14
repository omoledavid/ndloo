<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SignUpChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Total User Sign Up';

    protected static ?string $maxHeight = '300px';

    public ?string $filter = 'today';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'month' => 'This Month',
            'year' => 'This Year',
            'all' => 'All Time',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? 'today';
        
        $data = match ($filter) {
            'today' => $this->getTodaySignUps(),
            'month' => $this->getMonthlySignUps(),
            'year' => $this->getYearlySignUps(),
            'all' => $this->getAllTimeSignUps(),
            default => $this->getTodaySignUps(),
        };

        return [
            'datasets' => [
                [
                    'label' => 'User Sign Ups',
                    'data' => $data['values'],
                    'backgroundColor' => '#dc2626',
                    'borderColor' => '#dc2626',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getTodaySignUps(): array
    {
        $today = Carbon::today();
        
        // Get signups for each hour of today
        $signUps = User::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->whereDate('created_at', $today)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $labels = [];
        $values = [];
        
        // Initialize all 24 hours with 0
        for ($i = 0; $i < 24; $i++) {
            $labels[] = sprintf('%02d:00', $i);
            $values[] = 0;
        }

        // Populate with actual data
        foreach ($signUps as $signUp) {
            $values[$signUp->hour] = $signUp->count;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getMonthlySignUps(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        
        // Get signups for each day of the current month
        $signUps = User::selectRaw('DAY(created_at) as day, COUNT(*) as count')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $daysInMonth = $currentMonth->daysInMonth;
        $labels = [];
        $values = [];
        
        // Initialize all days of the month with 0
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $labels[] = $i;
            $values[] = 0;
        }

        // Populate with actual data
        foreach ($signUps as $signUp) {
            $values[$signUp->day - 1] = $signUp->count;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getYearlySignUps(): array
    {
        $currentYear = Carbon::now()->year;
        
        // Get signups for each month of the current year
        $signUps = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $labels = [];
        $values = [];
        
        // Initialize all months with 0
        foreach ($months as $num => $name) {
            $labels[] = $name;
            $values[] = 0;
        }

        // Populate with actual data
        foreach ($signUps as $signUp) {
            $values[$signUp->month - 1] = $signUp->count;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getAllTimeSignUps(): array
    {
        // Get signups for the last 5 years
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 4;
        
        $signUps = User::selectRaw('YEAR(created_at) as year, COUNT(*) as count')
            ->whereYear('created_at', '>=', $startYear)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $labels = [];
        $values = [];
        
        // Initialize all years with 0
        for ($year = $startYear; $year <= $currentYear; $year++) {
            $labels[] = $year;
            $values[] = 0;
        }

        // Populate with actual data
        foreach ($signUps as $signUp) {
            $index = array_search($signUp->year, $labels);
            if ($index !== false) {
                $values[$index] = $signUp->count;
            }
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}

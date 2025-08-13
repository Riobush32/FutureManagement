<?php

namespace App\Filament\Resources\BudgetResource\Widgets;

use App\Models\Budget;
use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ChartButgetingControl extends ChartWidget
{
    protected static ?string $heading = 'Budget Controlling';

    protected function getData(): array
    {
        $budgets = Budget::where('user_id', Auth::id())->get();

        $labels = [];
        $totalBudget = [];
        $usedBudget = [];
        $remainingBudget = [];

        foreach ($budgets as $budget) {
            $labels[] = $budget->name;

            $totalBudget[] = $budget->amount;
            $usedBudget[] = $budget->used_budget;
            $remainingBudget[] = $budget->amount - $budget->used_budget;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Budget',
                    'data' => $totalBudget,
                    'backgroundColor' => '#2196F3',
                ],
                [
                    'label' => 'Used Budget',
                    'data' => $usedBudget,
                    'backgroundColor' => '#f44336',
                ],
                [
                    'label' => 'Remaining Budget',
                    'data' => $remainingBudget,
                    'backgroundColor' => '#4CAF50',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // INI YANG MEMBUAT BAR CHART MENJADI HORIZONTAL
        ];
    }
}

<?php

namespace App\Filament\Resources\BudgetResource\Widgets;

use App\Models\Budget;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ChartBudgeting extends ChartWidget
{
    protected static ?string $heading = 'Budgeting';

    protected function getData(): array
    {
        $budgets = Budget::where('user_id', Auth::id())->get();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($budgets as $budget) {
            $labels[] = $budget->name;
            $data[] = $budget->amount;
            $colors[] = $this->generateSafeRandomColor();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Amount',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    private function generateSafeRandomColor(): string
    {
        // Hindari warna terlalu gelap atau terlalu terang
        $r = rand(50, 200);
        $g = rand(50, 200);
        $b = rand(50, 200);

        return "rgb($r, $g, $b)";
    }
}

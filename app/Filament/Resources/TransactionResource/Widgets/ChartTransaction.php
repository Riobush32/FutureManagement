<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ChartTransaction extends ChartWidget
{
    protected string|int|array $columnSpan = 'full';
    protected static ?string $heading = 'Daily Income & Expense';
    


    protected function getData(): array
    {
        // Ambil semua tanggal dalam 7 hari terakhir (bisa diubah)
        $dates = collect(range(0, 6))->map(function ($daysAgo) {
            return Carbon::today()->subDays($daysAgo)->format('Y-m-d');
        })->sort()->values();

        $incomeData = [];
        $expenseData = [];

        foreach ($dates as $date) {
            $income = Transaction::whereHas(
                'category',
                fn($q) =>
                $q->where('type', 'income')
            )
                ->whereDate('transaction_date', $date)
                ->where('user_id', Auth::user()->id)
                ->sum('amount');

            $expense = Transaction::whereHas(
                'category',
                fn($q) =>
                $q->where('type', 'expense')
            )
                ->whereDate('transaction_date', $date)
                ->where('user_id', Auth::id())
                ->sum('amount');

            $incomeData[] = $income;
            $expenseData[] = $expense;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $incomeData,
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Expense',
                    'data' => $expenseData,
                    'borderColor' => '#F44336',
                    'backgroundColor' => 'rgba(244, 67, 54, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $dates->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

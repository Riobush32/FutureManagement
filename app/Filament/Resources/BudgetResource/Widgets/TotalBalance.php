<?php

namespace App\Filament\Resources\BudgetResource\Widgets;

use App\Models\Wallet;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TotalBalance extends BaseWidget
{
    protected function getStats(): array
    {
        $totalIncome = Transaction::whereHas('category', function ($query) {
            $query->where('type', 'income');
        })->sum('amount');

        $totalExpense = Transaction::whereHas('category', function ($query) {
            $query->where('type', 'expense');
        })->sum('amount');

        //total semua saldo di wallet untuk pertama kali dimasukkan
        $allBalanceInWallet =  Wallet::sum('balance');

        $totalBalance = $allBalanceInWallet + $totalIncome - $totalExpense;

        return [
            Stat::make('Total Balance', "Rp " . number_format($totalBalance, 2))
                ->color('info')
                ->description('All Periode')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Total Income', "Rp ".number_format($totalIncome,2))
                ->color('success') 
                ->description('All Periode')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Total Expanse', "Rp " . number_format($totalExpense, 2))
                ->color('danger')
                ->description('All Periode')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}

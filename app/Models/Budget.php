<?php

namespace App\Models;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Budget extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    // logic
    public function getUsedBudgetAttribute(): float
    {
        return Transaction::whereHas('category', function ($query) {
            $query->where('type', 'expense')
                ->where('budget_id', $this->id);
        })->sum('amount');
    }

    public function getRemainingBudgetAttribute(): float
    {
        return $this->amount - $this->used_budget;
    }

    
}

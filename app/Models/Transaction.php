<?php

namespace App\Models;

use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['type', 'category', 'amount', 'date', 'note'])]
class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    /**
     * The most transactions one account may keep.
     *
     * The list itself is paginated now, so this no longer guards one slow page:
     * it bounds what the account as a whole can cost to read. The report of a
     * year is the heaviest of those readers, since it loads its period row by
     * row into the PDF, while the dashboard and the monthly statistics only ask
     * the database for sums. 2.000 rows is far more than a personal budget needs
     * (years of daily records) and still small enough to keep those reads quick.
     */
    public const MAX_PER_USER = 2000;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
        ];
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

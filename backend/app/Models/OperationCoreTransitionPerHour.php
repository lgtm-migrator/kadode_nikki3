<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationCoreTransitionPerHour extends Model
{
    use HasFactory;

    // updated_atは使わないので無効化
    public const updated_at = null;

    protected $fillable = [
        'user_total', 'diary_total', 'statistic_per_date_total', 'created_at',
    ];

    /**
     * $castsではtoArray,toJsonでUTCになってしまうため、アクセサで上書きする.
     */
    public function createdAt(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($value)->timezone('Asia/Tokyo')->format('Y-m-d H:i:s'),
        );
    }
}

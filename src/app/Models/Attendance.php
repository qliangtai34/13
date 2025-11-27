<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
    ];

    /**
     * Carbon キャスト（必須）
     */
    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    /**
     * 休憩とのリレーション
     */
    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function user()
{
    return $this->belongsTo(User::class);
}

}
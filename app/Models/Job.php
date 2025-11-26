<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Kolom-kolom ini yang diizinkan untuk diisi via Job::create()
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'frame_id',
        'name',
        'priority',
        'original_file',
        'result_url',
        'status',
    ];

    /**
     * Relasi ke model User
     * Job ini milik satu User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Frame
     * Job ini menggunakan satu Frame
     */
    public function frame()
    {
        return $this->belongsTo(Frame::class);
    }
}
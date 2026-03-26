<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriveFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'drive_folder_id',
        'name',
        'disk',
        'path',
        'mime_type',
        'extension',
        'size',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(DriveFolder::class, 'drive_folder_id');
    }
}

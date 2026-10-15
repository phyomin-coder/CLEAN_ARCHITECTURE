<?php

namespace App\Models;

use App\Api\Foundation\Traits\TracksUserActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Admin;

class ToDo extends Model
{
    use HasFactory, HasUuids, TracksUserActivity;
    protected $fillable = [
        'job',
        'description',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}

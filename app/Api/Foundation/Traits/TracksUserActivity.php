<?php

namespace App\Api\Foundation\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

use Illuminate\Support\Facades\Auth;

trait TracksUserActivity
{
    public static function bootTracksUserActivity(): void
    {
        Event::listen("eloquent.creating: " . static::class, function (Model $model): void {
            $actorId = Auth::id();
            $model->setAttribute("created_by", $actorId);
            $model->setAttribute("updated_by", $actorId);
        });
        Event::listen("eloquent.updating: " . static::class, function (Model $model): void {
            $model->setAttribute("updated_by", Auth::id());
        });
    }
}

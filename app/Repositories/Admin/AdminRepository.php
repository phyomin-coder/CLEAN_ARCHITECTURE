<?php

namespace App\Repositories\Admin;

use App\Api\Foundation\Repository\Eloquent\BaseRepository;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    public function optionsQuery(array $options): Builder
    {
        $query = parent::optionsQuery($options);

        if (isset($options['search'])) {
            $query = $query->where(function ($query) use ($options) {
                $query->orWhere('title', 'like', "%{$options['search']}%")
                    ->orWhere('description', 'like', "%{$options['search']}");
            });
        }

        return $query;
    }

    public function connection(): Admin
    {
        return new Admin;
    }
}

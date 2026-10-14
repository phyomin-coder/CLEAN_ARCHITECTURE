<?php

namespace App\Repositories\ToDo;

use App\Api\Foundation\Repository\Eloquent\BaseRepository;
use App\Models\ToDo;
use Illuminate\Database\Eloquent\Builder;
use Override;

class ToDoRepository extends BaseRepository implements ToDoRepositoryInterface
{

    public function optionsQuery(array $options): Builder
    {
        if (isset($options['sort_by']) && isset($options['order_by'])) {
            $direction = strtolower($options['order_by']) === 'asc' ? 'asc' : 'desc';
            $options['order_by'] = [$options['sort_by'] => $direction];
        }
        $query = parent::optionsQuery($options);

        if (isset($options['search'])) {
            $query = $query->where(function ($query) use ($options) {
                $query->orWhere('job', 'like', "%{$options['search']}%")
                    ->orWhere('description', 'like', "%{$options['search']}");
            });
        }

        return $query;
    }
    public function connection(): ToDo
    {
        return new ToDo;
    }
}

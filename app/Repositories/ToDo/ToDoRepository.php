<?php
namespace App\Repositories\ToDo;

use App\Api\Foundation\Repository\Eloquent\BaseRepository;
use App\Models\ToDo;
use Illuminate\Database\Eloquent\Builder;
use Override;

class ToDoRepository extends BaseRepository implements ToDoRepositoryInterface{
 
public function optionsQuery(array $options): Builder
    {
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
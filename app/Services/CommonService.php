<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CommonService
{
    protected int $offset = 0;

    protected int $limit = 10;

    public function input(Request $request, array $fillable): array
    {
        return array_filter($request->only($fillable), fn($value) => $value !== null);
    }

    public function params(Request $request, array $only = [], array $options = []): array
    {
        $search = $request->get('search');

        $params = [
            'search' => $search['value'] ?? null,
            'limit'  => !empty($request->get('limit')) ? (int) $request->get('limit') : $this->limit,
            'offset' => !empty($request->get('offset')) ? (int) $request->get('offset') : $this->offset,
        ];

        if (!empty($only)) {
            $params = collect($params)->only($only)->toArray();
        }

        if (!empty($options)) {
            $params = array_merge($params, $options);
        }

        return $params;
    }

    public function prepareInput($request): array
    {
        $input = [];

        if($request->hasFile('image')) {
            $image = $request->file('image');
            $input['image'] = str_replace('public/thumbnails/', '', $image->store('public/thumbnails'));
        }

        if ($request->has('password')) {
            $password = $request->input('password');
            $input['password'] = bcrypt($password);
        }

        return $input;
    }

    public function dtParams($request, $allow = null, array $options = []): array
    {
        $search = $request->get('search');
        $searchVal = $search['value'] ?? null;
        $limit = $request->get('length') ?? $this->limit;
        $offset = $request->get('start') ?? $this->offset;

        $params = ['limit' => $limit, 'offset' => $offset];

        if (!empty($allow)) {

            if (is_array($allow)) {
                foreach ($allow as $value) {
                    $params[$value] = $request->get($value);
                }
            } else {
                $params[$allow] = $request->get($allow);
            }
        }

        if (!empty($searchVal)) {
            $params['search'] = $searchVal;
        }

        if (!empty($options)) {
            $params = array_merge($params, $options);
        }

        return $params;
    }

    public function defaultDtCollection($data, $keys = [], $wantImage = false, $path = null, $keyName = null): Collection
    {
        return collect($data)->map(function ($value) use ($keys, $wantImage, $path, $keyName) {

            $data = [
                'id' => $value->id,
            ];

            if (!empty($keys)) {
                foreach ($keys as $key) {
                    $item = $value->$key;

                    if ($wantImage && $path && $key === $keyName) {
                        $data[$key] = $value->$keyName ? asset("storage/$path/$item") : asset("img/logo/default-logo.png");
                    } else {
                        $data[$key] = $value->$key ?? '---';
                    }
                }
            }

            return array_merge($data);
        });
    }

    public function currentGuard()
    {
        $guards = collect(array_values(config('helper.guards')));

        return $guards->first(fn($guard) => auth($guard)->check());
    }
}

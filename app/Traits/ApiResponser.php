<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

trait ApiResponser
{
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    protected function showAll(Collection $colection, $code = 200)
    {
        if ($colection->isEmpty()) {
            return $this->successResponse(['data' => $colection], $code);
        }

        $transformer = $colection->first()->transformer;
        $colection = $this->filterData($colection, $transformer);
        $colection = $this->sortData($colection, $transformer);
        $colection = $this->paginate($colection);
        $colection = $this->transformData($colection, $transformer);
        $colection = $this->cacheResponse($colection);

        return $this->successResponse($colection, $code);
    }

    protected function showOne(Model $instance, $code = 200)
    {
        $transformer = $instance->transformer;
        $instance = $this->transformData($instance, $transformer);
        return $this->successResponse($instance, $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    protected function filterData(Collection $colection, $transformer)
    {
        foreach (request()->query() as $query => $value) {
            $attribute = $transformer::originalAttribute($query);

            if (isset($attribute, $value)) {
                $colection = $colection->where($attribute, $value);
            }
        }
        return $colection;
    }

    protected function sortData(Collection $colection, $transformer)
    {
        if (request()->has('sort_by')) {
            $attribute = $transformer::originalAttribute(request()->sort_by);
            $colection = $colection->sortBy->{$attribute};    
        }

        return $colection;
    }

    protected function paginate(Collection $colection)
    {
        $rules = [
            'per_page' => 'integer|min:2|max:50',
        ];
        Validator::validate(request()->all(), $rules);
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        if (request()->has('per_page')) {
            $perPage = (int)request()->per_page;
        }
        $results = $colection->slice(($page - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator($results, $colection->count(), $perPage, $page, [
                'path' => LengthAwarePaginator::resolveCurrentPage(),

        ]);
        $paginated->appends(request()->all());
        return $paginated;
    }

    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);

        return $transformation->toArray();
    }

    protected function cacheResponse($data)
    {
        $url = request()->url();
        $queryParams = request()->query();

        ksort($queryParams);
        
        $queryString = http_build_query($queryParams);
        $fullUrl = "{$url}?{$queryString}";

        return Cache::remember($fullUrl, 30/60, function() use ($data) {
            return $data;
        });
    }
}
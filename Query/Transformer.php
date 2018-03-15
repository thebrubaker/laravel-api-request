<?php

namespace App\Http\Api\Query;

use App\Http\Api\Query\Field;
use Carbon\Carbon;

class Transformer
{
    /**
     * Transform request fields into a collection.
     * @param  string $query
     * @return Collection
     */
    public function fields($query)
    {
        if ($query === null) {
            return collect();
        }

        return collect(explode(',', $query));
    }

    /**
     * Transform request filters into a collection.
     * @param  string $query
     * @return Collection
     */
    public function filters($query)
    {
        if ($query === null) {
            return collect();
        }

        $filters = collect($query)->map(function($query, $key) {
            if (!is_array($query)) {
                return new Filter($key, $query);
            }

            return collect($query)->map(function($query, $type) use ($key) {
                return new Filter($key, $query, $type);
            })->flatten();
        })->flatten();

        return $filters;
    }

    /**
     * Transform request includes into a Collection
     * @param  array|null $query
     * @return Collection
     */
    public function includes($query)
    {
        if ($query === null) {
            return collect();
        }

        $includes = collect($query)->map(function($query, $key) {
            $with = new With($key);

            if (is_array($query)) {
                $with->fields = $this->fields($query['fields'] ?? null);
                $with->filters = $this->filters($query['filter'] ?? null);
                $with->sorting = $this->sort($query['sort'] ?? null);
            }

            return $with;
        })->flatten();

        return $includes;
    }

    /**
     * Transform request sort into collection
     * @param  array|null $query
     * @return Collection
     */
    public function sort($query)
    {
        if ($query === null) {
            return collect();
        }

        $sorting = collect($query)->map(function($order, $field) {
            return new Sort($field, $order);
        })->flatten();

        return $sorting;
    }
}

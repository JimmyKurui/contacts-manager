<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait QueryParametersTrait
{
    public function scopeParametize(Builder $query, array $payload): Builder
    {
        // ---- SEARCH --------------------------------------------------
        if (!empty($payload['q'] ?? null)) {
            $query->search($payload['q']);
        }

        // ---- FILTERING -----------------------------------------------
        if (!empty($payload['filters'] ?? [])) {
            $query->filter($payload['filters']);
        }

        // ---- SORTING --------------------------------------------------
        if (!empty($payload['sort'] ?? null)) {
            $query->sort($payload['sort'], $payload['order'] ?? 'asc');
        }

        return $query;
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $fields = $this->searchable ?? [];

        return $query->where(function ($q) use ($fields, $term) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%$term%");
            }
        });
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $allowed = $this->filterable ?? [];

        foreach ($filters as $field => $value) {
            if (in_array($field, $allowed)) {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    public function scopeSort(Builder $query, string $field, string $order = 'desc'): Builder
    {
        if (!in_array($field, $this->getSortableFields() ?? [])) {
            return $query;
        }

        return $query->orderBy($field, $order);
    }
}

<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersEventMonth implements Filter
{
    public function __invoke( Builder $query, $value, string $property)
    {
        $query->whereMonth('date', $value);
    }
}

<?php

namespace Modules\User\Services;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class PaginationService
{
    protected $maxPaginate = 100;


    public function paginate(Builder $query, $request, array $sortableColumns)
    {
        $paginate = $request->input('paginate', 10);
        $paginate = min($paginate, $this->maxPaginate);

        if (!in_array('id', $sortableColumns))
            $sortableColumns[] = 'id';

        $sortColumn = $request->input('sort', 'id');
        if (!$this->isSortable($sortColumn, $sortableColumns)) {
            response()->json(['msg' => 'The value you entered for sorting is invalid'], 400)->send();
            exit;
        }

        $sortDirection = Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
        $sortColumn = ltrim($sortColumn, '-');

        $tableName = $query->getModel()->getTable();

        return $query->orderBy("{$tableName}.{$sortColumn}", $sortDirection)->paginate($paginate);
    }

    protected function isSortable($column, array $sortableColumns)
    {
        return in_array(ltrim($column, '-'), $sortableColumns);
    }

}

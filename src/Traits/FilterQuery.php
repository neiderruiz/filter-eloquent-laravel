<?php

namespace Neiderruiz\Filtereloquentlaravel\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

trait FilterQuery
{
    public $actionWhere = ['like', '=', '==', '>=', '<=', '!=', '>', '<'];
    public $filter;

    function __construct($filter, $relations, $relations_count)
    {
        $this->filter = $filter;
        $this->relations = $relations;
        $this->relations_count = $relations_count;
    }

    static function clearQuery($itemRequest): string
    {
        $query = Str::replaceFirst('[', '', $itemRequest);
        return Str::replaceLast(']', '', $query);
    }

    public function addWithCount(): void
    {
        $query = request()->with_count;

        if (!is_null($query)) {

            $filters =  explode(',', $query);

            foreach ($filters as $filter) {
                $this->filter->withCount($filter);
            }
        }
    }

    public function addWith(): void
    {
        $query = request()->with;
        if (!is_null($query)) {

            $query = self::clearQuery($query);
            $filters = explode('][', $query);

            foreach ($filters as $filter) {

                $this->filter->with($filter);
            }
        }
    }

    public function addFiltersWith($arrayInputs, $required = true): void
    {
        [$table, $name, $action, $world] = $arrayInputs;

        if ($action === 'like') {
            $world =  "%$$world%";
        } else {
            $world = $world;
        }

        if ($required) {

            $this->filter->whereHas($table, function ($query) use ($name, $action, $world) {
                return $query->where($name, $action, $world);
            });
        } else {
            $this->filter->orWhereHas($table, function ($query) use ($name, $action, $world) {
                return $query->where($name, $action, $world);
            });
        }
    }

    public function generalFilter($filter)
    {

        $xFilter = explode(',', $filter);
        $relation = in_array($xFilter[0], $this->relations);
        $count = count($xFilter);

        if ($count === 1) return false;

        if ($relation) {
            $table = $xFilter[0];
            switch ($count) {
                case 2:

                    $nameInput = 'name';
                    $world = $xFilter[1];
                    $action = 'like';
                    $this->filter->whereHas($table, function ($query) use ($nameInput, $action, $world) {
                        return $query->where($nameInput, $action, "%$world%");
                    });

                    break;
                case 3:
                    $column = $xFilter[1];
                    $word = $xFilter[2];
                    $action = 'like';

                    $this->filter->whereHas($table, function (Builder $query) use ($column, $action, $word) {
                        return $query->where($column, $action,  "%$word%");
                    });

                    case 4:
                        $column = $xFilter[1];
                        $word = $xFilter[2];
                        $action = 'like';

                        $this->filter->whereHas($table, function (Builder $query) use ($column, $action, $word) {
                            return $query->where($column, $action,  "%$word%");
                        });

                    break;

                default:
                    # code...
                    break;
            }
        }

        if (!$relation && !in_array($xFilter[1], $this->relations_count)) {
            switch ($count) {
                case 2:
                    $this->filter->where($xFilter[0], 'like', "%$xFilter[1]%");
                    break;
                case 3:
                    if ($xFilter[2] == 'required') {

                        $this->filter->where($xFilter[0], 'like', "%$xFilter[1]%");
                    }

                    if (in_array($xFilter[2], $this->actionWhere)) {

                        $filter = $xFilter[2] == 'like' ? "%$xFilter[1]%" : $xFilter[1];

                        $this->filter->where($xFilter[0], $xFilter[2], $filter);
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }
    }
    public function filterWhere($filter)
    {

        $xFilter = explode(',', $filter);
        $relation = in_array($xFilter[0], $this->relations);
        $count = count($xFilter);
        if ($count === 1) return false;

        if ($relation) {
            $table = $xFilter[0];
            switch ($count) {
                case 2:
                    $nameInput = 'name';
                    $world = $xFilter[1];
                    $action = 'like';
                    $this->filter->whereHas($table, function ($query) use ($nameInput, $action, $world) {
                        return $query->where($nameInput, $action, $world);
                    });

                    break;
                case 3:
                    $column = $xFilter[1];
                    $word = $xFilter[2];

                    $this->filter->whereHas($table, function (Builder $query) use ($column, $word) {
                        return $query->where($column, $word);
                    });

                    break;

                case 4:
                    $column = $xFilter[1];
                    $word = $xFilter[2];
                    $action = $xFilter[3];
                    $this->filter->whereHas($table, function (Builder $query) use ($column, $action, $word) {
                        if($action === 'like')
                            return $query->where($column, $action, "%$word%");
                        else
                            return $query->where($column, $action, $word);
                    });

                    break;

                default:
                    # code...
                    break;
            }
        }

        if (!$relation && !in_array($xFilter[1], $this->relations_count)) {

            switch ($count) {

                case 2:
                    $column = $xFilter[0];
                    $word = $xFilter[1];
                    $this->filter->where($column, $word);
                    break;
                case 3:
                    $column = $xFilter[0];
                    $word = $xFilter[1];
                    $action = $xFilter[2];
                    if (in_array($action, $this->actionWhere)) {
                        if ($action === 'like') {
                            $this->filter->where($column, $action, "%$word%");
                        } else {
                            $this->filter->where($column, $action, $word);
                        }
                    }
                    break;
            }
        }
    }


    public function search(): void
    {

        $query = request()->search;
        if (!is_null($query)) {

            $query = self::clearQuery($query);

            $filters = explode('][', $query);
            foreach ($filters as $filter) {
                $this->generalFilter($filter);
            }
        }
    }

    public function select(): void
    {
        $query = request()->fields;
        if (!is_null($query)) {

            $inputs = explode(',', $query);
            $this->filter->select($inputs);
        }
    }

    public function limit(): void
    {
        $query = request()->limit;
        if (!is_null($query)) {

            $limit = request()->limit;
            $this->filter->take($limit);
        }
    }

    public function where(): void
    {
        $query = request()->where;
        if (!is_null($query)) {
            $query = self::clearQuery($query);

            $filters = explode('][', $query);
            foreach ($filters as $filter) {
                $this->filterWhere($filter);
            }
        }
    }

    public function paginate(): object
    {
        if (boolval(request()->paginate) && request()->paginate !== 'false') {
            $data =  $this->filter->paginate(request()->limit);
        } else {
            $data = $this->filter->get();
        }

        return $data;
    }

    public function first(): object
    {
        $data = $this->filter->first();


        return $data;
    }


    public function orderBy()
    {
        $query = request()->order_by;
        if (!is_null($query)) {

            $query = self::clearQuery($query);

            $filters = explode('][', $query);
            foreach ($filters as $filter) {
                $order = explode(',', $filter);
                $this->filter->orderBy($order[0], $order[1]);
            }
        }
    }

    public function filtersAll()
    {
        $this->select();
        $this->where();
        $this->addWithCount();
        $this->addWith();
        $this->search();
        $this->limit();
        $this->orderBy();
        return $this->paginate();
    }

    public function filtersFirst()
    {
        $this->select();
        $this->where();
        $this->addWithCount();
        $this->addWith();
        $this->search();
        $this->limit();
        $this->orderBy();
        return $this->first();
    }
}

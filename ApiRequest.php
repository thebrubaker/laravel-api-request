<?php

namespace App\Http\Api;

use App\Http\Api\Query\Filter;
use App\Http\Api\Query\Sort;
use App\Http\Api\Query\Transformer;
use App\Http\Api\Query\With;
use App\Http\Api\Traits\ResolvesQueryBuilder;
use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{
	use ResolvesQueryBuilder;

    /**
     * What fields should be included.
     * @var Collection
     */
    public $fields;

	/**
	 * How the resource should be filtered.
	 * @var Collection
	 */
	public $filters;

	/**
	 * How the resource should be sorted.
	 * @var Collection
	 */
	public $sorting;

	/**
	 * What resources should be included on the request.
	 * @var Collection
	 */
	public $includes;

	/**
	 * The number to show per page for paginated results.
	 * @var integer
	 */
	public $limit;

	/**
	 * The page number for paginated results.
	 * @var integer
	 */
	public $page;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
	    return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
	    return [];
	}

	/**
	 * Apply the scope from the API to a query.
	 * @param  mixed $mixed  Eloquent class string or query Builder instance.
	 * @return Builder
	 */
	public function scope($mixed)
	{
		$query = $this->resolve($mixed);
        $this->scopeFields($query, $this->fields);
		$this->scopeFilters($query, $this->filters);
		$this->scopeSorting($query, $this->sorting);
		$this->scopeIncludes($query, $this->includes);
		$this->scopePagination($query, $this->limit, $this->page);

		return $query;
	}

    /**
     * Apply filters to a query.
     * @param  Builder $query
     * @param  Collection $fields
     * @return void
     */
    public function scopeFields($query, $fields)
    {
        $fields->each(function ($field) use ($query) {
            $query->addSelect($field);
        });
    }

	/**
	 * Apply filters to a query.
	 * @param  Builder $query
     * @param  Collection $filters
	 * @return void
	 */
	public function scopeFilters($query, $filters)
	{
		$filters->each(function (Filter $filter) use ($query) {
			$query->where($filter->field, $filter->operator, $filter->value);
		});
	}

	/**
	 * Apply orders to a query.
	 * @param  Builder $query
     * @param  Collection $sorting
	 * @return void
	 */
	public function scopeSorting($query, $sorting)
	{
		$sorting->each(function (Sort $sort) use ($query) {
			$query->orderBy($sort->field, $sort->order);
		});
	}

	/**
	 * Apply includes to a query.
     * @param  Builder $query
	 * @param  Collection $includes
	 * @return void
	 */
	public function scopeIncludes($query, $includes)
	{
		$includes->each(function (With $relation) use ($query) {
			$query->with([$relation->name => function ($query) use ($relation) {
                $this->scopeFields($query, $relation->fields);
                $this->scopeFilters($query, $relation->filters);
                $this->scopeSorting($query, $relation->sorting);
            }]);
		});
	}

	/**
	 * Apply pagination to a query.
     * @param  Builder $query
     * @param  int $limit
	 * @param  int $page
	 * @return void
	 */
	public function scopePagination($query, $limit, $page)
	{
		$query->limit($limit);
		$query->offset($limit * ($page - 1));
	}
}

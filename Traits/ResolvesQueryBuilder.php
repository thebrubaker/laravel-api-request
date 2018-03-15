<?php 

namespace App\Http\Api\Traits;

use Api\QueryScopeBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait ResolvesQueryBuilder
{
	/**
	 * The argument may either be a class string for an Eloquent model
	 * or an instance of the query builder. We try and resolve the 
	 * former into the latter.
	 * @param  mixed $mixed
	 * @return Builder
	 */
	public function resolve($mixed)
	{
		// If it's a string, we assume an eloquent class is passed in.
		// Otherwise we assume it's a query object.
		if (is_string($mixed)) {
			$query = $this->resolveEloquentModel($mixed);
		} else {
			$query = $mixed;
		}

		if (!$query instanceof Builder) {
			throw new \Exception('Argument could not be resolved to instance of \Illuminate\Database\Eloquent\Builder');
		}

		return $query;
	}

	/**
	 * Resolve an eloquent class from the container.
	 * @param  string $class  The class name of the model.
	 * @return Builder
	 */
	public function resolveEloquentModel($class)
	{
		$model = $this->container->make($class);

		if (!$model instanceof Model) {
			throw new \Exception('Resolved class must be an instance of \Illuminate\Database\Eloquent\Model.');
		}

		return $model->query();
	}
}
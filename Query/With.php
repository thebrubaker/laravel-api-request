<?php

namespace App\Http\Api\Query;

use Illuminate\Support\Collection;

class With
{
    /**
     * The name of the relationship to include.
     * @var string
     */
    public $name;

    /**
     * The fields to select in the relationship.
     * @var Collection
     */
    public $fields;

    /**
     * The filters for the relationship
     * @var Collection
     */
    public $filters;

    /**
     * The sorting for the relationship
     * @var Collection
     */
    public $sorting;

    /**
     * The constructor for the class.
     * @param string $name
     */
    function __construct($name)
    {
        $this->name = $name;
        $this->fields = collect();
        $this->filters = collect();
        $this->sorting = collect();
    }
}

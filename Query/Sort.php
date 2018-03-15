<?php

namespace App\Http\Api\Query;

class Sort
{
    /**
     * The field to sort on.
     * @var string
     */
    public $field;

    /**
     * The order to sort by.
     * @var string
     */
    public $order;
    
    /**
     * The constructor for the Filter.
     * @param string $field
     * @param string $value
     * @param string $operator
     */
    function __construct($field, $order)
    {
        $this->field = $field;
        $this->order = $order ?? 'asc';
    }
}

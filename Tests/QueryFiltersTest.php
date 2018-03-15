<?php

namespace App\Http\Api\Tests;

use App\Http\Api\Query\Filter;
use App\Http\Api\Query\Transformer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class QueryFiltersTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        config(['timestamp_format' => 'U']);
        $this->transform = app(Transformer::class);
    }

    /**
     *      
     */
    public function test_no_filters()
    {
        $filters = $this->transform->filters(null);
        $this->assertTrue($filters instanceof Collection);
        $this->assertTrue($filters->isEmpty());
    }

    /**
     * 
     */
    public function test_two_filters()
    {
        $query = [
            'name' => 'test',
            'email' => 'test@test.com'
        ];
        $filters = $this->transform->filters($query);
        $this->assertTrue($filters instanceof Collection);
        $this->assertTrue($filters[0] instanceof Filter);
        $this->assertEquals($filters[0]->field, 'name');
        $this->assertEquals($filters[0]->operator, '=');
        $this->assertEquals($filters[0]->value, 'test');
        $this->assertTrue($filters[1] instanceof Filter);
        $this->assertEquals($filters[1]->field, 'email');
        $this->assertEquals($filters[1]->operator, '=');
        $this->assertEquals($filters[1]->value, 'test@test.com');
    }

    /**
     * 
     */
    public function test_filter_not_null()
    {
        $query = [
            'name' => null
        ];
        $filters = $this->transform->filters($query);
        $this->assertTrue($filters instanceof Collection);
        $this->assertEquals($filters->count(), 1);
        $this->assertTrue($filters[0] instanceof Filter);
        $this->assertEquals($filters[0]->field, 'name');
        $this->assertEquals($filters[0]->operator, '!=');
        $this->assertEquals($filters[0]->value, null);
    }

    /**
     * 
     */
    public function test_filter_is_null()
    {
        $query = [
            'name' => 'null'
        ];
        $filters = $this->transform->filters($query);
        $this->assertTrue($filters instanceof Collection);
        $this->assertEquals($filters->count(), 1);
        $this->assertTrue($filters[0] instanceof Filter);
        $this->assertEquals($filters[0]->field, 'name');
        $this->assertEquals($filters[0]->operator, '=');
        $this->assertEquals($filters[0]->value, null);
    }

    /**
     * 
     */
    public function test_timestamp_filters()
    {
        $query = [
            'created_at' => Carbon::now()->timestamp
        ];
        $filters = $this->transform->filters($query);
        $this->assertTrue($filters instanceof Collection);
        $this->assertEquals($filters->count(), 1);
        $this->assertTrue($filters[0] instanceof Filter);
        $this->assertEquals($filters[0]->field, 'created_at');
        $this->assertEquals($filters[0]->operator, '=');
        $this->assertTrue(is_int($filters[0]->value));
    }

    /**
     * 
     */
    public function test_min_filter()
    {
        $query = [
            'created_at' => [
                'min' => Carbon::now()->timestamp,
            ],
        ];
        $filters = $this->transform->filters($query);
        $this->assertTrue($filters instanceof Collection);
        $this->assertEquals($filters->count(), 1);
        $this->assertTrue($filters[0] instanceof Filter);
        $this->assertEquals($filters[0]->field, 'created_at');
        $this->assertEquals($filters[0]->operator, '>=');
        $this->assertTrue(is_int($filters[0]->value));
    }

    /**
     * 
     */
    public function test_min_max_filter()
    {
        $query = [
            'created_at' => [
                'min' => Carbon::now()->timestamp,
                'max' => Carbon::now()->timestamp,
            ],
        ];
        $filters = $this->transform->filters($query);
        $this->assertTrue($filters instanceof Collection);
        $this->assertEquals($filters->count(), 2);
        $this->assertTrue($filters[0] instanceof Filter);
        $this->assertEquals($filters[0]->field, 'created_at');
        $this->assertEquals($filters[0]->operator, '>=');
        $this->assertTrue(is_int($filters[0]->value));
        $this->assertTrue($filters[1] instanceof Filter);
        $this->assertEquals($filters[1]->field, 'created_at');
        $this->assertEquals($filters[1]->operator, '<=');
        $this->assertTrue(is_int($filters[1]->value));
    }

    /**
     * 
     */
    public function test_multiple_nested_filters()
    {
        $query = [
            'name' => 'foo',
            'created_at' => [
                'min' => Carbon::now()->timestamp,
                'max' => Carbon::now()->timestamp,
            ],
        ];
        $filters = $this->transform->filters($query);
        $this->assertTrue($filters instanceof Collection);
        $this->assertEquals($filters->count(), 3);
        $this->assertTrue($filters[0] instanceof Filter);
        $this->assertEquals($filters[0]->field, 'name');
        $this->assertEquals($filters[0]->operator, '=');
        $this->assertEquals($filters[0]->value, 'foo');
        $this->assertTrue($filters[1] instanceof Filter);
        $this->assertEquals($filters[1]->field, 'created_at');
        $this->assertEquals($filters[1]->operator, '>=');
        $this->assertTrue(is_int($filters[1]->value));
        $this->assertTrue($filters[2] instanceof Filter);
        $this->assertEquals($filters[2]->field, 'created_at');
        $this->assertEquals($filters[2]->operator, '<=');
        $this->assertTrue(is_int($filters[2]->value));
    }

    /**
     * 
     */
    public function test_contains_filter()
    {
        $query = [
            'status' => 'active,disabled'
        ];
        $filters = $this->transform->filters($query);
        $this->assertTrue($filters instanceof Collection);
        $this->assertEquals($filters->count(), 1);
        $this->assertTrue($filters[0] instanceof Filter);
        $this->assertEquals($filters[0]->field, 'status');
        $this->assertEquals($filters[0]->operator, '=');
        $this->assertEquals($filters[0]->value, ['active', 'disabled']);
    }
}

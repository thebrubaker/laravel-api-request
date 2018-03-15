<?php

namespace App\Http\Api\Tests;

use App\Http\Api\Query\Filter;
use App\Http\Api\Query\Sort;
use App\Http\Api\Query\Transformer;
use App\Http\Api\Query\With;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class QueryIncludesTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->transformer = app(Transformer::class);
    }

    /**
     *      
     */
    public function test_no_includes()
    {
        $includes = $this->transformer->includes(null);
        $this->assertTrue($includes instanceof Collection);
        $this->assertTrue($includes->isEmpty());
    }

    /**
     *      
     */
    public function test_simple_includes()
    {
        $query = [
            'comments' => null,
            'users.posts' => null,
        ];
        $includes = $this->transformer->includes($query);
        $this->assertTrue($includes instanceof Collection);
        $this->assertEquals($includes->count(), 2);
        $this->assertTrue($includes[0] instanceof With);
        $this->assertEquals($includes[0]->name, 'comments');
        $this->assertEquals($includes[0]->fields->count(), 0);
        $this->assertEquals($includes[0]->filters->count(), 0);
        $this->assertTrue($includes[1] instanceof With);
        $this->assertEquals($includes[1]->name, 'users.posts');
        $this->assertEquals($includes[1]->fields->count(), 0);
        $this->assertEquals($includes[1]->filters->count(), 0);
    }

    /**
     * 
     */
    public function test_includes_with_filters_and_fields()
    {
        $query = [
            'articles' => [
                'fields' => 'id,created_at',
                'filter' => [
                    'created_at' => Carbon::now()->timestamp,
                ],
                'sort' => [
                    'id' => null,
                ],
            ],
        ];
        $includes = $this->transformer->includes($query);
        $this->assertTrue($includes instanceof Collection);
        $this->assertEquals($includes->count(), 1);
        $this->assertTrue($includes[0] instanceof With);
        $this->assertEquals($includes[0]->name, 'articles');
        $this->assertEquals($includes[0]->fields->count(), 2);
        $this->assertEquals($includes[0]->fields[0], 'id');
        $this->assertEquals($includes[0]->fields[1], 'created_at');
        $this->assertTrue($includes[0]->filters[0] instanceof Filter);
        $this->assertEquals($includes[0]->filters[0]->field, 'created_at');
        $this->assertTrue(is_int($includes[0]->filters[0]->value));
        $this->assertEquals($includes[0]->filters[0]->operator, '=');
        $this->assertTrue($includes[0]->sorting[0] instanceof Sort);
        $this->assertEquals($includes[0]->sorting[0]->field, 'id');
        $this->assertEquals($includes[0]->sorting[0]->order, 'asc');
    }
}

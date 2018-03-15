<?php

namespace App\Http\Api\Tests;

use App\Http\Api\Query\Transformer;
use App\Http\Api\Query\Sort;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class QuerySortTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->transform = app(Transformer::class);
    }

    /**
     *      
     */
    public function test_no_sort_order()
    {
        $sorting = $this->transform->sort(null);
        $this->assertTrue($sorting instanceof Collection);
        $this->assertTrue($sorting->isEmpty());
    }

    /**
     * 
     */
    public function test_sorting()
    {
        $query = [
            'name' => 'asc',
            'age' => 'desc',
        ];
        $sorting = $this->transform->sort($query);
        $this->assertTrue($sorting instanceof Collection);
        $this->assertEquals($sorting->count(), 2);
        $this->assertTrue($sorting[0] instanceof Sort);
        $this->assertEquals($sorting[0]->field, 'name');
        $this->assertEquals($sorting[0]->order, 'asc');
        $this->assertTrue($sorting[1] instanceof Sort);
        $this->assertEquals($sorting[1]->field, 'age');
        $this->assertEquals($sorting[1]->order, 'desc');
    }

    /**
     * 
     */
    public function test_sorting_default_order()
    {
        $query = [
            'name' => null,
        ];
        $sorting = $this->transform->sort($query);
        $this->assertTrue($sorting instanceof Collection);
        $this->assertEquals($sorting->count(), 1);
        $this->assertTrue($sorting[0] instanceof Sort);
        $this->assertEquals($sorting[0]->field, 'name');
        $this->assertEquals($sorting[0]->order, 'asc');
    }
}

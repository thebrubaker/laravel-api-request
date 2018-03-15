<?php

namespace App\Http\Api\Tests;

use App\Http\Api\Query\Field;
use App\Http\Api\Query\Transformer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class QueryFieldsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->transform = app(Transformer::class);
    }

    /**
     *      
     */
    public function test_no_fields()
    {
        $fields = $this->transform->fields(null);
        $this->assertTrue($fields instanceof Collection);
        $this->assertTrue($fields->isEmpty());
    }

    /**
     * 
     */
    public function test_fields()
    {
        $query = 'name,created_at';
        $fields = $this->transform->fields($query);
        $this->assertTrue($fields instanceof Collection);
        $this->assertEquals($fields->count(), 2);
        $this->assertEquals($fields[0], 'name');
        $this->assertEquals($fields[1], 'created_at');
    }
}

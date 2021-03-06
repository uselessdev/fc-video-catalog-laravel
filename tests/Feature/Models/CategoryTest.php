<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();

        $this->assertCount(1, $categories);

        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(['id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'], $categoryKeys);
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'Category'
        ]);

        $category->refresh();
        $isValidUuid = Str::isUuid($category->id);

        $this->assertTrue($isValidUuid);
        $this->assertEquals('Category', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'Category',
            'description' => null
        ]);

        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'Category',
            'description' => 'category description'
        ]);

        $this->assertEquals('category description', $category->description);

        $category = Category::create([
            'name' => 'Category',
            'is_active' => false
        ]);

        $this->assertFalse($category->is_active);
    }

    public function testUpdate()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create([
            'description' => 'test description',
            'is_active' => false,
        ]);

        $data = [
            'name' => 'test name',
            'description' => 'test desc',
            'is_active' => true,
        ];

        $category->update($data);

        foreach($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        $category = factory(Category::class)->create();

        $category->delete();
        $this->assertNull(Category::find($category->id));

        $category->restore();
        $this->assertNotNull(Category::find($category->id));
    }
}

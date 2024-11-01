<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        $category =  Category::create($data);

        return $this->successResponse([
            'name' => $category->name,
            'slug' => $category->slug
        ], 'Category created successfully');
    }

    /**
     * Display the list resource.
     */
    public function list(Category $category)
    {
        $category = Category::orderBy('id', 'desc')->get(['id', 'name', 'slug']);
        return $this->successResponse($category, 'Category get successfully');
    }

    /**
     * Display the specified resource.
     */
    public function single(string $slug)
    {
        $category = Category::where('slug', $slug)->first();
        return $this->successResponse($category, 'Category get successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request)
    {
        $data = $request->validated();
        $category = Category::find($request->id);
        $category->update($data);
        return $this->successResponse([
            'name' => $category->name,
            'slug' => $category->slug
        ], 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $result = Category::destroy($id);
        if (!$result) {
            return $this->errorResponse('Category not found', 404);
        }
        return $this->successResponse(true, 'Category deleted successfully');
    }
}

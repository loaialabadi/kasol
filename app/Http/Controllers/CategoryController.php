<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            if (!is_numeric($perPage) || $perPage <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'The per_page parameter must be a positive number'
                ], 400);
            }

            if (!is_numeric($page) || $page <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'The page parameter must be a positive number'
                ], 400);
            }

            $categories = Category::with('subCategories')->paginate($perPage, ['*'], 'page', $page);

            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No categories found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'massage' => 'Categories fetched successfully',
                'data' => CategoryResource::collection($categories),
                'pagination' => [
                    'total' => $categories->total(),
                    'per_page' => $categories->perPage(),
                    'current_page' => $categories->currentPage(),
                    // 'last_page' => $categories->lastPage(),
                    'pages_count' => $categories->lastPage(),
                ],
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching categories',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $category = Category::with('subCategories')->findOrFail($id);

            return response()->json([
                'success' => true,
                'massage' => 'Category fetched successfully',
                'data' => new CategoryResource($category)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function categories_page(){
        $categories=Category::get();
        return view('categories/categories_page',compact('categories'));
    }
    public function add_new_category(){
        return view('categories.add_new_category');
    }

}

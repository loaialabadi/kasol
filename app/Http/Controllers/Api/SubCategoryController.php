<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubCategoryResource;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the subcategories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $categoryId = $request->query('category_id');

            if (!$categoryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'category_id is required'
                ], 400);
            }

            $subCategories = SubCategory::where('category_id', $categoryId)
                ->with('category')->get();

            if ($subCategories->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No subcategories found for the given category',
                    'data'=>[],
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message'=> 'Subcategories fetched successfully',
                'data' => SubCategoryResource::collection($subCategories),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while fetching subcategories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified subcategory.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $subCategory = SubCategory::with('category')->findOrFail($id);
            return response()->json([
                'success' => true,
                'message'=> 'Subcategory fetched successfully',
                'data' => new SubCategoryResource($subCategory)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'SubCategory not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the subcategory',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

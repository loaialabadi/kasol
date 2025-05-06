<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class RestaurantController extends Controller
{
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

            $restaurants = Restaurant::with([
                'products',
                'offers',
                'images',
                'branches',
                'user.stories' => function ($query) {
                    $query->withoutGlobalScopes();
                },
                'user'
            ])
                ->paginate($perPage, ['*'], 'page', $page);

            if ($restaurants->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No restaurants found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Restaurants fetched successfully',
                'data' => RestaurantResource::collection($restaurants),
                'pagination' => [
                    'total' => $restaurants->total(),
                    'per_page' => $restaurants->perPage(),
                    'current_page' => $restaurants->currentPage(),
                    'pages_count' => $restaurants->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while fetching restaurants',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $restaurant = Restaurant::with(['products', 'offers', 'images', 'branches', 'user.stories', 'user'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Restaurant fetched successfully',
                'data' => new RestaurantResource($restaurant)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found'
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching restaurant',
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


    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|unique:restaurants,name',
    //         'address' => 'nullable|string',
    //         'phone' => 'nullable|string',
    //         'email' => 'nullable|email',
    //         'baner' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    //         'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    //         'menu' => 'nullable|mimes:pdf|max:2048',
    //         'status' => 'nullable|in:active,inactive',
    //         'description' => 'nullable|string',
    //         'price_range' => 'nullable|string',
    //         'start_work_date' => 'nullable|date_format:H:i',
    //         'end_work_date' => 'nullable|date_format:H:i',
    //         'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $restaurant = Restaurant::create([
    //         'name' => $request->name,
    //         'address' => $request->address,
    //         'phone' => $request->phone,
    //         'email' => $request->email,
    //         'baner' => $request->file('baner') ? $request->file('baner')->store('restaurants/banner', 'public') : null,
    //         'logo' => $request->file('logo') ? $request->file('logo')->store('restaurants/logo', 'public') : null,
    //         'menu' => $request->file('menu') ? $request->file('menu')->store('restaurants/menu', 'public') : null,
    //         'status' => $request->status ?? 'active',
    //         'description' => $request->description,
    //         'price_range' => $request->price_range,
    //         'start_work_date' => $request->start_work_date,
    //         'end_work_date' => $request->end_work_date,
    //     ]);

    //     if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $imageFile) {
    //             $imagePath = $imageFile->store('restaurants/images', 'public');
    //             Images::create([
    //                 'image' => $imagePath,
    //                 'restaurant_id' => $restaurant->id,
    //             ]);
    //         }
    //     }

    //     return response()->json(new RestaurantResource($restaurant), 201);
    // }

}

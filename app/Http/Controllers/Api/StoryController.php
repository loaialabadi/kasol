<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    /**
     * Display a listing of the stories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $stories = Story::latest()->get();

        return response()->json([
            'success' => true,
            'massage' => 'Stories fetched successfully',
            'stories' => $stories,
        ]);
    }

    /**
     * Display the specified story.
     *
     * @param int $storyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($storyId)
    {
        $story = Story::findOrFail($storyId);

        return response()->json([
            'success' => true,
            'massage' => 'Story fetched successfully',
            'story' => $story,
        ]);
    }

    /**
     * Store a newly uploaded story.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:20480',
            'service_id' => 'required|exists:services,id',
        ]);

        $filePath = $request->file('file')->store('stories', 'public');

        $story = Story::create([
            'user_id' => Auth::id(),
            'file' => $request->file('file')->getClientOriginalName(),
            'file_path' => $filePath,
            'service_id' => $validated['service_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Story uploaded successfully!',
            'story' => $story,
        ], 201);
    }

    /**
     * Update the specified story.
     *
     * @param Request $request
     * @param int $storyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $storyId)
    {
        $story = Story::findOrFail($storyId);

        if ($story->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        if ($request->hasFile('file')) {
            $validated = $request->validate([
                'file' => 'required|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:20480', // Max 20MB
                'sevice_id' => 'required|exists:services,id',
            ]);

            $filePath = $request->file('file')->store('stories', 'public');

            $story->update([
                'file' => $request->file('file')->getClientOriginalName(),
                'service_id' => $validated['service_id'],
                'file_path' => $filePath,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Story updated successfully!',
            'story' => $story,
        ]);
    }

    /**
     * Remove the specified story.
     *
     * @param int $storyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($storyId)
    {
        $story = Story::findOrFail($storyId);

        if ($story->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        if (\Storage::disk('public')->exists($story->file_path)) {
            \Storage::disk('public')->delete($story->file_path);
        }

        $story->delete();

        return response()->json([
            'success' => true,
            'message' => 'Story deleted successfully!',
        ]);
    }
}

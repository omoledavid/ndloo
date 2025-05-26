<?php

namespace App\Http\Controllers\Livestream;

use App\Events\LivestreamStatusChanged;
use App\Events\ViewerCountUpdated;
use App\Http\Controllers\Controller;
use App\Models\Livestream;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LivestreamController extends Controller
{
    public function index()
    {
        $livestreams = Livestream::with(['user', 'categories'])
            ->where('is_live', true)
            ->orderBy('viewer_count', 'desc')
            ->paginate(10);
            
        return response()->json($livestreams);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'ticket_amount' => 'nullable|numeric|min:0',
            'goal_title' => 'nullable|string|max:255',
            'goal_amount' => 'nullable|numeric|min:0',
            'key_words' => 'nullable|string|max:255',
        ]);
        
        $streamKey = Str::random(20);
        
        $livestream = Livestream::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'stream_key' => $streamKey,
            'is_live' => true,
            'started_at' => now(),
            'ticket_amount' => $request->ticket_amount,
            'goal_title' => $request->goal_title,
            'goal_amount' => $request->goal_amount,
            'key_words' => $request->key_words,
        ]);
        
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $livestream->update(['thumbnail' => $path]);
        }
        
        if ($request->has('categories')) {
            $livestream->categories()->sync($request->categories);
        }
        
        return response()->json($livestream->load('categories'), 201);
    }
    
    public function show($id)
    {
        $livestream = Livestream::with(['user', 'categories', 'comments.user'])
            ->findOrFail($id);
            
        return response()->json($livestream);
    }
    
    public function update(Request $request, $id)
    {
        $livestream = Livestream::findOrFail($id);
        
        // Check if user owns this livestream
        if ($livestream->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);
        
        $livestream->update([
            'title' => $request->title ?? $livestream->title,
            'description' => $request->description ?? $livestream->description,
        ]);
        
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $livestream->update(['thumbnail' => $path]);
        }
        
        if ($request->has('categories')) {
            $livestream->categories()->sync($request->categories);
        }
        
        return response()->json($livestream->load('categories'));
    }
    
   public function startStream($id)
    {
        $livestream = Livestream::findOrFail($id);
        
        // Check if user owns this livestream
        if ($livestream->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $livestream->update([
            'is_live' => true,
            'started_at' => now(),
            'ended_at' => null,
        ]);
        
        // Load user relationship for broadcasting
        $livestream->load('user');
        
        // Broadcast the livestream status change
        broadcast(new LivestreamStatusChanged($livestream));
        
        return response()->json($livestream);
    }
    
    public function endStream($id)
    {
        $livestream = Livestream::findOrFail($id);
        
        // Check if user owns this livestream
        if ($livestream->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $livestream->update([
            'is_live' => false,
            'ended_at' => now(),
        ]);
        
        // Load user relationship for broadcasting
        $livestream->load('user');
        
        // Broadcast the livestream status change
        broadcast(new LivestreamStatusChanged($livestream));
        
        return response()->json($livestream);
    }
    
    public function updateViewerCount(Request $request, $id)
    {
        $livestream = Livestream::findOrFail($id);
        
        $request->validate([
            'viewer_count' => 'required|integer|min:0',
        ]);
        
        $livestream->update([
            'viewer_count' => $request->viewer_count,
        ]);
        
        // Broadcast the viewer count update
        broadcast(new ViewerCountUpdated($livestream));
        
        return response()->json($livestream);
    }
    
    public function destroy($id)
    {
        $livestream = Livestream::findOrFail($id);
        
        // Check if user owns this livestream
        if ($livestream->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $livestream->delete();
        
        return response()->json(['message' => 'Livestream deleted successfully']);
    }
}
<?php

namespace App\Http\Controllers\Livestream;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        return response()->json($user);
    }
    
    public function getLivestreams($id)
    {
        $user = User::findOrFail($id);
        
        $livestreams = $user->livestreams()
            ->with('categories')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return response()->json($livestreams);
    }
    
    public function getCurrentLivestream($id)
    {
        $user = User::findOrFail($id);
        
        $livestream = $user->livestreams()
            ->with(['categories', 'comments' => function($query) {
                $query->with('user')->latest()->limit(20);
            }])
            ->where('is_live', true)
            ->first();
            
        if (!$livestream) {
            return response()->json(['message' => 'User is not currently live'], 404);
        }
        
        return response()->json($livestream);
    }
    
    public function follow($id)
    {
        $user = User::findOrFail($id);
        
        // Can't follow yourself
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'You cannot follow yourself'], 400);
        }
        
        // Check if already following
        $existingFollow = Follow::where('follower_id', auth()->id())
            ->where('following_id', $user->id)
            ->first();
            
        if ($existingFollow) {
            return response()->json(['message' => 'Already following this user'], 400);
        }
        
        $follow = Follow::create([
            'follower_id' => auth()->id(),
            'following_id' => $user->id,
        ]);
        
        return response()->json($follow, 201);
    }
    
    public function unfollow($id)
    {
        $user = User::findOrFail($id);
        
        $follow = Follow::where('follower_id', auth()->id())
            ->where('following_id', $user->id)
            ->firstOrFail();
            
        $follow->delete();
        
        return response()->json(['message' => 'Unfollowed successfully']);
    }
    
    public function getFollowers($id)
    {
        $user = User::findOrFail($id);
        
        $followers = $user->followers()
            ->with('follower')
            ->paginate(20);
            
        return response()->json($followers);
    }
    
    public function getFollowing($id)
    {
        $user = User::findOrFail($id);
        
        $following = $user->following()
            ->with('following')
            ->paginate(20);
            
        return response()->json($following);
    }
}
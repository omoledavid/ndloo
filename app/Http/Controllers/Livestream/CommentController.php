<?php

namespace App\Http\Controllers\Livestream;

use App\Events\NewComment;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Livestream;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($livestreamId)
    {
        $livestream = Livestream::findOrFail($livestreamId);
        
        $comments = $livestream->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($comments);
    }
    
    public function store(Request $request, $livestreamId)
    {
        $livestream = Livestream::findOrFail($livestreamId);
        
        $request->validate([
            'content' => 'required|string|max:500',
        ]);
        
        $comment = Comment::create([
            'user_id' => auth()->id(),
            'livestream_id' => $livestream->id,
            'content' => $request->content,
        ]);
        
        // Load the user relationship for broadcasting
        $comment->load('user');
        
        // Broadcast the new comment event
        broadcast(new NewComment($comment))->toOthers();
        
        return response()->json($comment->load('user'), 201);
    }
    
    public function destroy($livestreamId, $commentId)
    {
        $comment = Comment::where('livestream_id', $livestreamId)
            ->where('id', $commentId)
            ->firstOrFail();
            
        // Check if user owns this comment or is the livestream owner
        $livestream = Livestream::findOrFail($livestreamId);
        if ($comment->user_id !== auth()->id() && $livestream->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $comment->delete();
        
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
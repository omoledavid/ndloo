<?php

namespace App\Http\Controllers\Livestream;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        
        return response()->json($categories);
    }
    
    public function show($id)
    {
        $category = Category::findOrFail($id);
        
        return response()->json($category);
    }
    
    public function getLivestreams($id)
    {
        $category = Category::findOrFail($id);
        
        $livestreams = $category->livestreams()
            ->with('user')
            ->where('is_live', true)
            ->orderBy('viewer_count', 'desc')
            ->paginate(10);
            
        return response()->json($livestreams);
    }
}
<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // $posts = json_decode(Storage::get('data\posts.json'),true);

        $posts = Post::orderByRaw('created_at DESC')->get();

        $featured_post = true;
        foreach ($posts as &$value) {
            $value->featured_post = $featured_post;
            $featured_post = false;
        }

        return Inertia::render('Home', [
            'posts' => $posts, // Passa os posts para a view
        ]);

    }
}

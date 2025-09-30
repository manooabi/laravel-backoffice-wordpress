<?php

namespace App\Http\Controllers;

use App\Models\PostPriority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{
    //
      public function index(Request $request)
    {
        $token = $request->get('wp_token'); // <-- use request, not session
    
        $site_id = env('WP_SITE_ID'); // set your site ID in .env

          $response = Http::withToken($token)
                    ->get("https://public-api.wordpress.com/rest/v1.1/sites/{$site_id}/posts/");

$posts = $response->json()['posts'];

    // Get local priorities
    $priorities = PostPriority::pluck('priority', 'wp_post_id');

    // Merge priorities into posts (default 0 if not set)
    foreach ($posts as &$post) {
        $post['priority'] = $priorities[$post['ID']] ?? 0;
    }
                    
        // return response()->json($response->json());
        return response()->json([
        'found' => count($posts),
        'posts' => $posts
    ]);
    }
   public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string',
        'content' => 'required|string',
    ]);

    $token = $request->get('wp_token');
    $site_id = env('WP_SITE_ID');

    $response = Http::withToken($token)
                    ->asJson()
                    ->post("https://public-api.wordpress.com/rest/v1.1/sites/{$site_id}/posts/new", [
                        'title' => $request->title,
                        'content' => $request->content,
                    ]);

    return response()->json([
        'status' => $response->status(),
        'data' => $response->json(),
    ]);
}
  public function show(Request $request, $id)
    {
    $token = $request->get('wp_token'); // <-- use request, not session
    $site_id = env('WP_SITE_ID');

        $response = Http::withToken($token)
                        ->get("https://public-api.wordpress.com/rest/v1.1/sites/{$site_id}/posts/{$id}");

        return response()->json($response->json());
    }
      public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string',
            'content' => 'sometimes|required|string',
        ]);

       $token = $request->get('wp_token'); // <-- use request, not session
    $site_id = env('WP_SITE_ID');

        $data = [];
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('content')) $data['content'] = $request->content;

        $response = Http::withToken($token)
                        ->asJson()
                        ->post("https://public-api.wordpress.com/rest/v1.1/sites/{$site_id}/posts/{$id}", $data);

        return response()->json($response->json());
    }
      public function destroy(Request $request, $id)
    {
       $token = $request->get('wp_token'); // <-- use request, not session
    $site_id = env('WP_SITE_ID');

        $response = Http::withToken($token)
                        ->post("https://public-api.wordpress.com/rest/v1.1/sites/{$site_id}/posts/{$id}/delete");

        return response()->json($response->json());
    }
    public function setPriority(Request $request, $id)
{
    $request->validate([
        'priority' => 'required|integer|min:0',
    ]);

    $priority = PostPriority::updateOrCreate(
        ['wp_post_id' => $id],
        ['priority' => $request->priority]
    );

    return response()->json(['message' => 'Priority updated', 'priority' => $priority->priority]);
}
 
    public function indexByPriority(Request $request)
{
    $token = $request->get('wp_token');
    $site_id = env('WP_SITE_ID');

    // 1️⃣ Get posts from WordPress
    $response = Http::withToken($token)
                    ->get("https://public-api.wordpress.com/rest/v1.1/sites/{$site_id}/posts/");

    $posts = $response->json()['posts'];

    // 2️⃣ Get local priorities
    $priorities = PostPriority::pluck('priority', 'wp_post_id');

    // 3️⃣ Merge priorities into posts
    foreach ($posts as &$post) {
        $post['priority'] = $priorities[$post['ID']] ?? null;
    }

    // 4️⃣ Sort posts by priority
    usort($posts, function ($a, $b) {
        if ($a['priority'] === null && $b['priority'] === null) return 0;
        if ($a['priority'] === null) return 1;
        if ($b['priority'] === null) return -1;
        return $b['priority'] <=> $a['priority'];
    });

    // ✅ Return the sorted posts
    return response()->json([
        'found' => count($posts),
        'posts' => $posts
    ]);
}
public function getPriority($id)
{
    $priority = PostPriority::where('wp_post_id', $id)->first();
    return response()->json([
        'wp_post_id' => $id,
        'priority' => $priority ? $priority->priority : null
    ]);
}

}

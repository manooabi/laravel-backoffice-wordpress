<?php

namespace App\Http\Controllers;

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
                    ->withOptions(['verify' => false])
                    ->get("https://public-api.wordpress.com/rest/v1.1/sites/{$site_id}/posts/");


        return response()->json($response->json());
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
                 ->withOptions(['verify' => false]) // disable SSL check
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
                        ->withOptions(['verify' => false])
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
                         ->withOptions(['verify' => false])
                        ->asJson()
                        ->post("https://public-api.wordpress.com/rest/v1.1/sites/{$site_id}/posts/{$id}", $data);

        return response()->json($response->json());
    }
      public function destroy(Request $request, $id)
    {
       $token = $request->get('wp_token'); // <-- use request, not session
    $site_id = env('WP_SITE_ID');

        $response = Http::withToken($token)
                        ->withOptions(['verify' => false])
                        ->post("https://public-api.wordpress.com/rest/v1.1/sites/{$site_id}/posts/{$id}/delete");

        return response()->json($response->json());
    }
}

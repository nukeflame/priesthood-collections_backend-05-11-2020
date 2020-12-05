<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\Post as PostResource;
use App\Http\Requests\Post\PostRequest;
use App\Models\PromoProduct;
use App\Models\Product;
use App\Http\Resources\Product\ProductCollection;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $post = Post::latest()->get();
        return new PostCollection($post);
    }

    /**
      * Display a listing of the resource.
      *
      * @return \Illuminate\Http\Response
      */
    public function getSliderPosts()
    {
        $posts = Post::where(['post_type' => 'normalPost','post_visibility' => 1, 'status' => 'publish'])->orderBy('created_at', 'desc')->get();
        return new PostCollection($posts);
    }


    /**
      * Display a listing of the resource.
      *
      * @return \Illuminate\Http\Response
      */
    public function getHomepagePosts()
    {
        $posts = Post::where(['post_type' => 'homepageAd','post_visibility' => 1, 'status' => 'publish'])->orderBy('created_at', 'desc')->first();
        return new PostResource($posts);
    }

    

    /**
      * Display a listing of the resource.
      *
      * @return \Illuminate\Http\Response
      */
    public function getfeaturedPosts()
    {
        $posts = Post::where(['post_type' => 'featuredPost','post_visibility' => 1, 'status' => 'publish'])->orderBy('created_at', 'desc')->get();
        return new PostCollection($posts);
    }
  
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $post = new Post();
        if ($request->postType === 'page') {
            $post->post_author = $request->authorId;
            $post->post_date = Carbon::now()->format('Y-m-d g:i:s');
            $post->content = $request->description;
            $post->title = $request->title;
            $post->status = 'publish';
            $post->slug = $request->title;
            $post->comment_status = 0;
            $post->post_parent = 0;
            $post->guid = 'https://www.example.com/url-slug';
            $post->menu_order = 0;
            $post->post_type = $request->postType;
            $post->comment_count = 0;
            $post->save();
        } elseif ($request->postType === 'featuredPost') {
            $post->post_author = $request->authorId;
            $post->post_date = Carbon::now()->format('Y-m-d g:i:s');
            $post->title = $request->title;
            $post->status = 'draft';
            $post->slug = $request->slug;
            $post->comment_status = 0;
            $post->post_parent = 0;
            $post->menu_order = 0;
            $post->post_type = $request->featuredType;
            $post->comment_count = 0;
            $post->post_visibility = $request->postVisibility;
            $post->save();
        }

        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);
        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function findOfferProducts($name)
    {
        $post = Post::where(['post_type' => 'normalPost','post_visibility' => 1, 'slug' => $name])->first();
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return;
        }
        if ($request->postType === 'featuredPost') {
            $post->post_author = $request->authorId;
            $post->title = $request->title;
            $post->slug = $request->slug;
            $post->post_type = $request->featuredType;
            $post->post_visibility = $request->postVisibility;
            $post->updated_at = now();
            $post->update();
        }

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Http\Resources\Post\Post as PostResource;
use App\Http\Resources\Post\PostCollection;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = Post::where(['post_type' => 'page', 'status' => 'publish','post_visibility' => 1])->orderBy('created_at', 'desc')->get();
        return new PostCollection($pages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $page =  new Post();
        $page->post_author = $request->authorId;
        $page->post_date = Carbon::now()->format('Y-m-d g:i:s');
        $page->content = $request->description;
        $page->title = $request->title;
        $page->status = 'publish';
        $page->slug =  Str::slug($request->title, '-');
        $page->post_type = $request->postType;
        $page->comment_status = 0;
        $page->post_parent = 0;
        $page->guid =  env('APP_URL') . '/' . Str::slug($request->title, '-');
        $page->menu_order = 0;
        $page->comment_count = 0;
        $page->post_visibility = 1;
        $page->save();
        
        return  new PostResource($page);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
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

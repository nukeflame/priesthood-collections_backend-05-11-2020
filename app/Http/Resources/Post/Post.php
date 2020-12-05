<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\User as UserResource;
use App\Http\Resources\Post\PromoProductCollection;

class Post extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'author' => new UserResource($this->author),
            'postDate' => $this->post_date,
            'content' => $this->content,
            'title' => $this->title,
            'status' => $this->status,
            'commentStatus' => $this->comment_status,
            'slug' => $this->slug,
            'postParent' => $this->post_parent,
            'guid' => $this->guid,
            'menuOrder' => $this->menu_order,
            'postType' => $this->post_type,
            'commentCount' => $this->comment_count,
            'postVisibility' => $this->post_visibility,
            'promoProducts' => new PromoProductCollection($this->promoProducts),
            'createdAt' => $this->created_at,
        ];
    }
}

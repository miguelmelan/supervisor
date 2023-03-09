<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Spatie\Tags\Tag;

class TagController extends Controller
{
    public function getTags()
    {
        $tags = Tag::query()
            ->when(request('search'), function ($query, $search) {
                $searchUCFirst = strtoupper(substr($search, 0, 1)) . substr($search, 1);
                $query->where('name', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$searchUCFirst}%");
            })->get();

        $tags = $tags->map->only(['name', 'slug'])->toArray();
        /* $tags = array_map(function ($tag) {
            return array(
                'value' => $tag['name'],
                'key' => $tag['slug'],
            );
        }, $tags->toArray()); */

        return Response::json($tags);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use JeroenG\Explorer\Application\Explored;

class Post extends Model implements Explored
{
    use Searchable;

    protected $fillable = [
        'title',
        'content'
    ];

    public function searchableAs(): string
    {
        return 'posts';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
        ];
    }

    public function mappableAs(): array
    {
        return [
            'id' => [
                'type' => 'keyword'
            ],
            'title' => [
                'type' => 'text'
            ],
            'content' => [
                'type' => 'text'
            ]
        ];
    }
}
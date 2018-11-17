<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    protected $table = 'articles';

    public function comments()
    {
        return $this->hasMany('App\ArticleComment', 'article_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany('App\ArticleTag', 'article_id', 'id');
    }

}

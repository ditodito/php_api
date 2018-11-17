<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class ArticleController extends Controller {

    public function index(Request $request)
    {
        // $sort = $request->input('sort', 'created_at');
        // $order = $request->input('order', 'desc');
        $sort = $this->getCorrectField(['created_at', 'comment_count'], $request->input('sort', 'created_at'), 'created_at');
        $order = $this->getCorrectField(['asc', 'desc'], $request->input('order', 'desc'), 'desc');
        $paginate = $request->input('paginate', null);
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        // $offset = ($page - 1) * $limit;

        $query = DB::table('articles')
            ->select('id', 'title', 'created_at',
                DB::raw('(SELECT count(*) FROM article_comment WHERE article_comment.article_id = articles.id) AS comment_count')
            )
            ->orderBy($sort, $order);

        if ($paginate) {
            $query->paginate((int) $paginate);
            // $query->offset($offset);
            // $query->limit($limit);
        } else {
            $query->limit((int) $limit);
        }

        $rows = $query->get();

        $results = [];
        foreach ($rows as $row) {
            $article = new stdClass();
            $article->id = $row->id;
            $article->title = $row->title;
            $article->created_at = $row->created_at;
            $article->comment_count = $row->comment_count;
            // $article->comment_count = DB::table('article_comment')->where('article_id', $row->id)->count();
            $article->tags = $this->getArticleTags($row->id);
            $results[] = $article;
        }

        return $results;
    }

    public function tagsArticles(Request $request, $id) {
        // $sort = $request->input('sort', 'created_at');
        // $order = $request->input('order', 'desc');
        $sort = $this->getCorrectField(['created_at', 'comment_count'], $request->input('sort', 'created_at'), 'created_at');
        $order = $this->getCorrectField(['asc', 'desc'], $request->input('order', 'desc'), 'desc');
        $paginate = $request->input('paginate', null);
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $query = DB::table('tags')
            ->select('articles.id', 'articles.title', 'articles.created_at',
                DB::raw('(SELECT count(*) FROM article_comment WHERE article_comment.article_id = articles.id) AS comment_count')
            )
            ->join('article_tag', 'article_tag.tag_id', '=', 'tags.id')
            ->join('articles', 'articles.id', '=', 'article_tag.article_id')
            ->where('tags.id', '=', (int) $id)
            ->orderBy($sort, $order);

        if ($paginate) {
           $query->paginate((int) $paginate);
        } else {
            $query->limit((int) $limit);
        }

        $rows = $query->get();

        $results = [];
        foreach ($rows as $row) {
            $article = new stdClass();
            $article->id = $row->id;
            $article->title = $row->title;
            $article->created_at = $row->created_at;
            $article->comment_count = $row->comment_count;
            $article->tags = $this->getArticleTags($row->id);
            $results[] = $article;
        }

        return $results;
    }

    public function comments(Request $request, $id) {
        // $sort = $request->input('sort', 'created_at');
        // $order = $request->input('order', 'desc');
        $sort = $this->getCorrectField('created_at', $request->input('sort', 'created_at'), 'created_at');
        $order = $this->getCorrectField(['asc', 'desc'], $request->input('order', 'desc'), 'desc');

        $results = DB::table('comments')
            ->select('comments.*')
            ->join('article_comment', 'article_comment.comment_id', '=', 'comments.id')
            ->where('article_comment.article_id', '=', (int) $id)
            ->orderBy('comments.' . $sort, $order)
            ->get();

        return $results;
    }

    public function tags(Request $request) {
        // $sort = $request->input('sort', 'article_count');
        // $order = $request->input('order', 'desc');
        $sort = $this->getCorrectField('article_count', $request->input('sort', 'article_count'), 'article_count');
        $order = $this->getCorrectField(['asc', 'desc'], $request->input('order', 'desc'), 'desc');

        $results = DB::table('tags')
            ->select('id', 'title',
                DB::raw('(SELECT count(*) FROM article_tag WHERE article_tag.tag_id = tags.id) AS article_count')
            )
            ->orderBy($sort, $order)
            ->get();

        return $results;
    }

    private function getArticleTags($id) {
        $results = DB::table('tags')
            ->select('tags.*')
            ->join('article_tag', 'tags.id', '=', 'article_tag.tag_id')
            ->where('article_tag.article_id', '=', $id)
            ->get();

        return $results;
    }

    private function getCorrectField($fields, $value, $default) {
        $data = !is_array($fields) ? (array) $fields : $fields;

        if (!in_array($value, $data)) {
            return $default;
        }

        return $value;
    }

}



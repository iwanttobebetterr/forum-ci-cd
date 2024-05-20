<?php

use App\Models\Comment;

it('generates the html', function () {
    $comment = Comment::factory()->create(['body' => '## Hello']);

    expect($comment->html)->toEqual(str($comment->body)->markdown());
});

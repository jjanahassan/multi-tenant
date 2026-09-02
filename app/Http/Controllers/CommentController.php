<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Events\CommentAdded;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreCommentRequest $request, Task $task){
        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        CommentAdded::dispatch($comment, auth()->user());

        return back()-> with('success', 'Comment added successfully.');
    }

    public function destroy(Comment $comment){
        abort_unless($comment->user_id === auth()->id(), 403);

        $comment->delete();
        return back()-> with('success', 'Comment deleted successfully.');
    }
}

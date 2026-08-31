<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentVote;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Publica um comentário (ou resposta a outro comentário) em um post.
     */
    public function store(Request $request)
    {
        $mensagens = [
            'content.required' => __('O comentário não pode ser vazio.'),
            'content.max' => __('O comentário deve ter no máximo 2000 caracteres.'),
            'post_id.exists' => __('Post inválido.'),
            'parent_id.exists' => __('Comentário pai inválido.'),
        ];

        $request->validate([
            'content' => 'required|string|min:1|max:2000',
            'post_id' => 'required|integer|exists:posts,id',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ], $mensagens);

        // Só é possível comentar em posts publicados
        $post = Post::publicado()->findOrFail($request->post_id);

        if ($request->parent_id) {
            $parent = Comment::findOrFail($request->parent_id);
            abort_if($parent->post_id !== $post->id, 422, 'O comentário pai pertence a outro post.');
        }

        Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => trim($request->content),
        ]);

        return back()->with('success', 'Comentário publicado!');
    }

    /**
     * Exclui um comentário (somente o dono).
     */
    public function destroy(Comment $comment)
    {
        abort_unless($comment->user_id === Auth::id(), 403, 'Você só pode excluir os seus próprios comentários.');

        $comment->delete();

        return back()->with('success', 'Comentário excluído!');
    }

    /**
     * Alterna o upvote do usuário no comentário (um voto por usuário).
     */
    public function vote(Comment $comment)
    {
        $voto = CommentVote::where('comment_id', $comment->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($voto) {
            $voto->delete();
        } else {
            CommentVote::create([
                'comment_id' => $comment->id,
                'user_id' => Auth::id(),
            ]);
        }

        return back();
    }
}

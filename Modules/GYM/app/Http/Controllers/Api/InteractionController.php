<?php

namespace Modules\GYM\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\GymGalleryMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InteractionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/user-app/interaction/like",
     *     summary="Toggle like/unlike for a media or other interactable",
     *     tags={"Interactions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="likeable_id", type="string"),
     *             @OA\Property(property="likeable_type", type="string", description="gym_media, etc.")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function toggleLike(Request $request)
    {
        $request->validate([
            'likeable_id' => 'required|string',
            'likeable_type' => 'required|string|in:gym_media,comment',
        ]);

        $typeMap = [
            'gym_media' => GymGalleryMedia::class,
            'comment' => Comment::class,
        ];

        $modelType = $typeMap[$request->likeable_type];
        $userId = auth()->id();

        $like = Like::where([
            'user_id' => $userId,
            'likeable_id' => $request->likeable_id,
            'likeable_type' => $modelType,
        ])->first();

        if ($like) {
            $like->delete();
            return response()->json([
                'success' => true,
                'message' => 'Unliked successfully.',
                'is_liked' => false
            ]);
        }

        Like::create([
            'user_id' => $userId,
            'likeable_id' => $request->likeable_id,
            'likeable_type' => $modelType,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Liked successfully.',
            'is_liked' => true
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user-app/interaction/comment",
     *     summary="Add a comment or reply",
     *     tags={"Interactions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="commentable_id", type="string"),
     *             @OA\Property(property="commentable_type", type="string", description="gym_media, etc."),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="parent_id", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Comment created")
     * )
     */
    public function storeComment(Request $request)
    {
        $request->validate([
            'commentable_id' => 'required|string',
            'commentable_type' => 'required|string|in:gym_media',
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $typeMap = [
            'gym_media' => GymGalleryMedia::class,
        ];

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'commentable_id' => $request->commentable_id,
            'commentable_type' => $typeMap[$request->commentable_type],
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment posted successfully.',
            'data' => $comment->load('user:id,name,image')
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/user-app/interaction/comments",
     *     summary="Get comments for a media or other interactable",
     *     tags={"Interactions"},
     *     @OA\Parameter(name="commentable_id", in="query", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="commentable_type", in="query", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function getComments(Request $request)
    {
        $request->validate([
            'commentable_id' => 'required|string',
            'commentable_type' => 'required|string|in:gym_media',
        ]);

        $typeMap = [
            'gym_media' => GymGalleryMedia::class,
        ];

        $comments = Comment::where([
            'commentable_id' => $request->commentable_id,
            'commentable_type' => $typeMap[$request->commentable_type],
            'parent_id' => null,
            'status' => 'approved'
        ])
        ->with(['user:id,name,image', 'replies.user:id,name,image'])
        ->withCount('likes')
        ->latest()
        ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Comments retrieved successfully.',
            'data' => $comments
        ]);
    }
}

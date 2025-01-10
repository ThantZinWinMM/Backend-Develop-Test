<?php

namespace App\Http\Controllers;

use App\Exceptions\UserAlreadyLikedPostException;
use App\Exceptions\UserLikeOwnPostException;
use App\Http\Requests\PostToggleReactionRequest;
use App\Http\Resources\PostCollection;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function list()
    {
        $posts = Post::withCount('likes')->with('tags')->paginate();

        return new PostCollection($posts);
    }

    public function toggleReaction(PostToggleReactionRequest $request)
    {
        //     try {
        //         $post = Post::query()
        //             ->with([
        //                 'likes' => function (HasMany $query) {
        //                     $query->whereBelongsTo(Auth::user());
        //                 },
        //             ])
        //             ->findOrFail($request->validated('post_id'));

        //         // user tries to like his own post
        //         throw_if(Gate::denies('like-post', $post), UserLikeOwnPostException::class);

        //         // user already liked the post
        //         if ($post->likes->isNotEmpty()) {
        //             // reaction is like the post
        //             throw_if($request->boolean('like'), UserAlreadyLikedPostException::class);

        //             $post->likes->map->delete();

        //             return response()->json([
        //                 'status'  => Response::HTTP_OK,
        //                 'message' => 'You unlike this post successfully',
        //             ]);
        //         }

        //         $post->likes()->create([
        //             'user_id' => Auth::id(),
        //         ]);

        //         return response()->json([
        //             'status'  => Response::HTTP_OK,
        //             'message' => 'You like this post successfully',
        //         ]);
        //     } catch (UserLikeOwnPostException $e) {
        //         return response()->json([
        //             'status'  => Response::HTTP_INTERNAL_SERVER_ERROR,
        //             'message' => 'You cannot like your post',
        //         ], Response::HTTP_INTERNAL_SERVER_ERROR);
        //     } catch (UserAlreadyLikedPostException $e) {
        //         return response()->json([
        //             'status'  => Response::HTTP_INTERNAL_SERVER_ERROR,
        //             'message' => 'You already liked this post',
        //         ], Response::HTTP_INTERNAL_SERVER_ERROR);
        //     } catch (ModelNotFoundException $e) {
        //         return response()->json([
        //             'status'  => Response::HTTP_NOT_FOUND,
        //             'message' => 'model not found',
        //         ], Response::HTTP_NOT_FOUND);
        //     } catch (\Throwable $e) {
        //         return response()->json([
        //             'status'  => Response::HTTP_INTERNAL_SERVER_ERROR,
        //             'message' => $e->getMessage(),
        //         ], Response::HTTP_INTERNAL_SERVER_ERROR);
        //     }



        try {

            // Fetch post with conditional likes for the authenticated user
            $post = Post::query()
                ->with([
                    'likes' => function (HasMany $query) {
                        $query->whereBelongsTo(Auth::user());
                    },
                ])
                ->findOrFail($request->validated('post_id'));

            // Ensure user cannot like their own post
            if (Gate::denies('like-post', $post)) {
                throw new UserLikeOwnPostException('You cannot like your own post.');
            }

            // Handle already liked post
            if ($post->likes->isNotEmpty()) {
                if ($request->boolean('like')) {
                    throw new UserAlreadyLikedPostException('You have already liked this post.');
                }

                // Unlike the post
                $post->likes()->delete();

                return $this->successResponse('You unliked this post successfully.');
            }

            // Like the post
            $post->likes()->create(['user_id' => Auth::id()]);

            return $this->successResponse('You liked this post successfully.');
        } catch (UserLikeOwnPostException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UserAlreadyLikedPostException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Post not found.', Response::HTTP_NOT_FOUND);
        } catch (\Throwable $e) {
            return $this->errorResponse('An unexpected error occurred.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function successResponse(string $message, int $status = Response::HTTP_OK): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status'  => $status,
            'message' => $message,
        ], $status);
    }

    protected function errorResponse(string $message, int $status): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status'  => $status,
            'message' => $message,
        ], $status);
    }
}

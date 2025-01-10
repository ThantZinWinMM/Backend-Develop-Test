<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Resources\AdminLoginResource;
use App\Models\Admin;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminLoginController extends Controller
{
    public function adminLogin(AdminLoginRequest $request)
    {
        try {
            $admin = Admin::where('email', $request->validated('email'))->firstOrFail();

            if (!Auth::guard('admin')->attempt($request->validated())) {
                throw new AuthenticationException('Invalid credentials');
            }

            // return AdminLoginResource::make($admin);
            return response()->json([
                'status' => Response::HTTP_OK,
                'data'   => new AdminLoginResource($admin),
            ], Response::HTTP_OK);
        } catch (AuthenticationException $e) {
            return response()->json([
                'status'  => Response::HTTP_UNAUTHORIZED,
                'message' => $e->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => Response::HTTP_NOT_FOUND,
                'message' => 'Model not found.',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Internal server error.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function test(Request $request)
    {
        dd('This is test for new guard user is work.');
    }
}

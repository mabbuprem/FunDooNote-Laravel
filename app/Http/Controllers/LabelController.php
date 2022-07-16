<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Lable;
use App\Exceptions\FundooNoteException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LabelController extends Controller
{


     /**
     * @OA\Post(
     *   path="/api/createlabel",
     *   summary="create label",
     *   description="create label",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"label_name"},
     *               @OA\Property(property="label_name", type="string"),          
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="label created Sucessfully"),
     *   @OA\Response(response=401, description="Invalid token"),
     * security={
     *       {"Bearer": {}}
     *     }
     * )
     * Create Label.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function createLabel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'labelname' => 'required|string|between:2,20',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->tojson(), 400);
            }
            $currentUser = JWTAuth::authenticate($request->token);
            $user_id = $currentUser->id;
            
            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new FundooNoteException('Invalid Authorization Token', 401);
            } else {
                $label = Lable::create([
                    'label_name' => $request->labelname,
                    'user_id' => $user_id,
                ]);
                Log::info('Lable successfully created');
                return response()->json([
                    'status' => 200,
                    'message' => 'Lable successfully created',
                    'label' => $label
                ]);
            }
        } catch (FundooNoteException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }




    /**
     * @OA\Post(
     *   path="/api/getLableById",
     *   summary="Read Label",
     *   description=" Read Label ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="label found Sucessfully"),
     *   @OA\Response(response=404, description="label not Found"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * Read label by id
     * @return \Illuminate\Http\JsonResponse
     */

    function getLableById(Request $request)
    {
        try {

            $validator = Validator::make($request->only('id'), [
                'id' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid'], 400);
            }

            $currentUser = JWTAuth::authenticate($request->token);

            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new FundooNoteException('Invalid Authorization Token', 401);
            }

            $currentid = $currentUser->id;
            $label = Lable::where('user_id', $currentid)->where('id', $request->id)->first();

            if (!$label) {
                Log::info('Label Not Found');
                throw new FundooNoteException('Label Not Found', 404);
            } else {
                return response()->json(['label' => $label], 201);
            }
        } catch (FundooNoteException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }



    /**
     *   @OA\Get(
     *   path="/api/getAllLabel",
     *   summary="read labels",
     *   description="user read labels",
     *   @OA\RequestBody(
     *    ),
     *   @OA\Response(response=201, description="labels shown suucessfully"),
     *   @OA\Response(response=401, description="No label created by this user"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */


    public function getAllLabel(Request $request)
    {
        try {
            $user = JWTAuth::authenticate($request->token);
            $label = Lable::all();

            if (!$user) {
                Log::error('Invalid Authorization Token');
                throw new FundooNoteException('Invalid Authorization Token', 401);
            }
            $label = Lable::where('user_id', $user->id)->get();

            if (!$label) {
                Log::error('Label Not Found');
                throw new FundooNoteException('Label Not Found', 404);
            } else {
                Log::info('Label Retrived Successfully');
                return response()->json([
                    'status' => 200,
                    'label' => $label
                ]);
            }
        } catch (FundooNoteException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    /**
     *   @OA\Post(
     *   path="/api/updateLabelById",
     *   summary="update label",
     *   description="update user label",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"Updated_name","id"},
     *               @OA\Property(property="Updated_name", type="string"),
     *               @OA\Property(property="id"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Label successfully updated"),
     *   @OA\Response(response=402, description="labels not found"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */


    public function updateLabelById(Request $request)
    {
         try {
            $validator = Validator::make($request->all(), [
                'updated_label' => 'required|string|between:2,30',
                'id' => 'required|integer',
            ]);
            // if ($validator->fails()) {
            //     return response()->json($validator->errors()->toJson(), 400);
            // }
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid'], 400);
            }

            $user = JWTAuth::authenticate($request->token);

            if (!$user) {
                Log::error('Invalid Authorization Token');
                throw new FundooNoteException('Invalid Authorization Token', 401);
            }

            $label = Lable::where('user_id', $user->id)->where('id', $request->id)->first();
           // return response()->json(['label' => $label], 200);

            if (!$label) {
                Log::error('Label Not Found');
                throw new FundooNoteException('Label Not Found', 404);
            }

            $label->label_name = $request->updated_label;
            // $label->user_id = $request->id;
            $label->save();
            
            $label->update([
                'label_name' => $request->updated_label,
            
            ]);
            Cache::forget('labels');
            Cache::forget('notes');

            return response()->json([
                'message' => 'label updated successfully',
                'label' => $label,
            ], 201);
        } catch (FundooNoteException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
         }
    }


    /**
     *   @OA\Delete(
     *   path="/api/deleteLabelById",
     *   summary="delete label",
     *   description="delete user label",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="label successfully deleted"),
     *   @OA\Response(response=404, description="labels not found"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */


    function deleteLabelById(Request $request)
    {
        try {

            $validator = Validator::make($request->only('id'), [
                'id' => 'required|integer'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid'], 400);
            }

            $currentUser = JWTAuth::authenticate($request->token);

            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new FundooNoteException('Invalid Authorization Token', 401);
            }

            $label = Lable::where('id', $request->id)->first();

            if (!$label) {
                Log::error('Label Not Found');
                throw new FundooNoteException('Label Not Found', 404);
            } else {
                // Cache::forget('labels');
                // Cache::forget('notes');

                $label->delete($label->id);
                return response()->json([
                    'mesaage' => 'label deleted Successfully',
                ], 200);
            }
        } catch (FundooNoteException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

}

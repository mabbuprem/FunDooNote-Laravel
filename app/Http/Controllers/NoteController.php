<?php

namespace App\Http\Controllers;
use App\Models\Lable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Note;
use App\Models\LableNotes;
use Illuminate\Support\Facades\Log;
use App\Exceptions\FundooNoteException;
class NoteController extends Controller
{

     /**
     * @OA\Post(
     *   path="/api/createnote",
     *   summary="create note",
     *   description="create note",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"title","description"},
     *               @OA\Property(property="title", type="string"),
     *               @OA\Property(property="description", type="string"),
     *                   
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note created Sucessfully"),
     *   @OA\Response(response=401, description="Invalid token"),
     * security={
     *       {"Bearer": {}}
     *     }
     * )
     * Create Note.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function createNote(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|min:3|max:30',
                'description' => 'required|string|min:3|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $currentUser = JWTAuth::authenticate($request->token);
            $user_id = $currentUser->id;

            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new FundooNoteException('Invalid Authorization Token', 401);
            } else {

                $note = Note::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'user_id' => $user_id,

                ]);
                return response()->json([
                    'message' => 'Note created successfully',
                    'note' => $note
                ], 200);
            }
        } catch (FundooNoteException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

     /**
     * @OA\Post(
     *   path="/api/getNoteById",
     *   summary="Read Note",
     *   description=" Read Note ",
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
     *   @OA\Response(response=201, description="Note found Sucessfully"),
     *   @OA\Response(response=404, description="Notes not Found"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * Read note by id
     * @return \Illuminate\Http\JsonResponse
     */


    function getNoteById(Request $request)
    {

        $validator = Validator::make($request->only('id'), [
            'id' => 'required|integer',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid'], 400);
        }

        $currentUser = JWTAuth::authenticate($request->token);

        if (!$currentUser) {
            return response()->json([
                'message' => 'Invalid Authorization Token',
            ], 401);
        }

        $currentid = $currentUser->id;
        //$note = Note::where('id', $request->id)->first();
        $note = Note::where('user_id', $currentid)->where('id', $request->id)->first();

        if (!$note) {
            return response()->json([
                'message' => 'Invalid id'
            ], 401);
        } else {
            return response()->json(['note' => $note], 200);
        }
    }


    /**
     *   @OA\Get(
     *   path="/api/getAllNotes",
     *   summary="read notes",
     *   description="user read notes",
     *   @OA\RequestBody(
     *    ),
     *   @OA\Response(response=201, description="Notes shown suucessfully"),
     *   @OA\Response(response=401, description="No note created by this user"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This function takes access token and note id and finds
     * if there is any note existing on that User id and note id if so
     * it successfully returns that note id
     *
     * @return \Illuminate\Http\JsonResponse
     */



    function getAllNotes(Request $request)
    {
        try {

            $currentUser = JWTAuth::authenticate($request->token);

            if (!$currentUser) {
                return response()->json([
                    'message' => 'Invalid Authorization Token',
                ], 401);
            }
            $notes = Note::getAllNotes($currentUser);

            if (!$notes) {
                return response()->json([
                    'message' => 'No note created by this user',
                ], 401);
            } else {
                return response()->json([
                    'notes' => $notes,
                ], 200);
            }
        } catch (FundooNoteException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }


     /**
     *   @OA\Post(
     *   path="/api/updateNoteById",
     *   summary="update note",
     *   description="update user note",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"Updated_title","Updated_description","id"},
     *               @OA\Property(property="Updated_title", type="string"),
     *               @OA\Property(property="Updated_description", type="string"),
     *               @OA\Property(property="id"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note successfully updated"),
     *   @OA\Response(response=402, description="Notes not found"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This function takes the User access token and note id which
     * user wants to update and finds the note id if it is existed
     * or not if so, updates it successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */



    public function updateNoteById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|between:2,30',
                'description' => 'required|string|between:3,1000',
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user = JWTAuth::authenticate($request->token);

            if (!$user) {

                throw new FundooNoteException('Invalid Authorization Token', 401);
            }

            $note = Note::where('user_id', $user->id)->where('id', $request->id)->first();

            if (!$note) {
                throw new FundooNoteException('Notes Not Found', 404);
            }

            // $note->update([
            //     'title' => $request->title,
            //     'description' => $request->description,
            //     'user_id' => $user->id,
            // ]);
            $note->title = $request->title;
            $note->description = $request->description;
            $note->save();

            return response()->json([
                'status' => 200,
                'note' => $note,
                'mesaage' => 'Note Successfully updated',
            ]);
        } catch (FundooNoteException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }


    /**
     *   @OA\Delete(
     *   path="/api/deleteNoteById",
     *   summary="delete note",
     *   description="delete user note",
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
     *   @OA\Response(response=200, description="Note successfully deleted"),
     *   @OA\Response(response=404, description="Notes not found"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This function takes the User access token and note id which
     * user wants to delete and finds the note id if it is existed
     * or not if so, deletes it successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function deleteNoteById(Request $request)
    {

        try {

            $validator = Validator::make($request->only('id'), [
                'id' => 'required|integer',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid'], 400);
            }

            $currentUser = JWTAuth::authenticate($request->token);

            if (!$currentUser) {
                log::warning('Invalid Authorisation Token ');
                throw new FundooNoteException('Invalid Authorization Token', 401);
            }

            $note = Note::where('id', $request->id)->first();

            if (!$note) {
                Log::error('Notes Not Found');
                throw new FundooNoteException('Notes Not Found', 404);
            } else {
                $note->delete($note->id);
                return response()->json([
                    'mesaage' => 'Note deleted Successfully',
                ], 200);
            }
        } catch (FundooNoteException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    public function searchNotes(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'search' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $searchKey = $request->input('search');
            $currentUser = JWTAuth::parseToken()->authenticate();

            if ($currentUser) {
                $usernotes = Note::getSearchedNote($searchKey, $currentUser);

                if ($usernotes == '[]') {
                    return response()->json([
                        'message' => 'Notes Not Found'
                    ], 404);
                }
                return response()->json([
                    'message' => 'Fetched Notes Successfully',
                    'notes' => $usernotes
                ], 200);
            }
            Log::error('Invalid Authorization Token');
            throw new FundooNoteException('Invalid Authorization Token', 401);
        } catch (FundooNoteException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    public function addNoteLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'note_id' => 'required',
            'label_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid authorization token'
            ], 401);
        }

        $labelnote = LableNotes::where('note_id', $request->note_id)->where('label_id', $request->label_id)->first();
        if ($labelnote) {
            return response()->json([
                'status' => 400,
                'message' => 'Note Already have a label'
            ], 409);
        }

        //$notelabel = LabelNotes::createNoteLabel($request, $user->id);
        $labelnotes = LableNotes::create([
            'user_id' => $user->id,
            'note_id' => $request->note_id,
            'label_id' => $request->label_id
        ]);
        log::info('Label created Successfully');
        return response()->json([
            'status' => 200,
            'message' => 'Label and note added Successfully',
            'notelabel' => $labelnotes,
        ]);
    }
    

    
    
}
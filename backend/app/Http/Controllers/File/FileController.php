<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\File\UploadFileRequest;
use App\Http\Resources\UserFileResource;
use App\Models\UserFile;
use App\Services\FileService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private readonly FileService $fileService) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->integer('per_page', config('files.per_page')), config('files.max_per_page'));

        return UserFileResource::collection(
            $this->fileService->list($request->user(), $request->string('search'), $perPage)
        )->response();
    }

    public function store(UploadFileRequest $request): JsonResponse
    {
        $files = $this->fileService->store($request->user(), $request->file('files'));

        return UserFileResource::collection(collect($files))->response()->setStatusCode(201);
    }

    public function download(UserFile $userFile): StreamedResponse
    {
        $this->authorize('download', $userFile);

        return $this->fileService->download($userFile);
    }

    public function destroy(UserFile $userFile): JsonResponse
    {
        $this->authorize('delete', $userFile);
        $this->fileService->delete($userFile);

        return response()->json(null, 204);
    }
}

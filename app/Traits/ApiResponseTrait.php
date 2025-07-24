<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    private bool $_success = true;
    private bool $_failed = false;

    /**
     * @param $message
     * @param $statusCode
     * @return JsonResponse
     */
    public function returnError($message, $statusCode): JsonResponse
    {
        return response()->json([
            "status" => $this->_failed,
            "message" => $message
        ], $statusCode);
    }

    /**
     * @param $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function returnSuccessMessage($message, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            "status" => $this->_success,
            "message" => $message
        ], $statusCode);
    }

    /**
     * @param $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function returnData($data, string $message = "success", int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            "status" => $this->_success,
            "message" => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function returnValidationError($validator): JsonResponse
    {
        return $this->returnError($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

}

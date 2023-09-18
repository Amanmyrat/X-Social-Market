<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App;

class ApiBaseController extends Controller
{
    /**
     * @var int $statusCode
     */
    protected int $statusCode = 200;

    const CODE_WRONG_ARGS = 'GEN-FUBARGS';
    const CODE_NOT_FOUND = 'GEN-LIKETHEWIND';
    const CODE_INTERNAL_ERROR = 'GEN-AAAGGH';
    const CODE_UNAUTHORIZED = 'GEN-MAYBGTFO';
    const CODE_FORBIDDEN = 'GEN-GTFO';

    /**
     * @var Manager $fractal
     */
    protected Manager $fractal;

    public function __construct(Request $request)
    {
        $this->fractal = new Manager;

        $locale = $request->header('X-Localization') ?? 'tm';
        App::setLocale($locale);
    }

    /**
     * Get the status code.
     *
     * @return int $statusCode
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set the status code.
     *
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Respond a no content response.
     *
     * @return JsonResponse
     */
    public function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Respond the item data.
     *
     * @param $item
     * @param $callback
     * @param string $message
     * @return JsonResponse
     */
    public function respondWithItem($item, $callback, string $message = 'Successfully'): JsonResponse
    {
        $resource = new Item($item, $callback);

        $data = $this->fractal->createData($resource)->toArray();

        $data['message'] = $message;
        $data['success'] = true;

        return $this->respondWithArray($data);
    }

    /**
     * Respond the collection data.
     *
     * @param $collection
     * @param $callback
     * @param string $message
     * @return JsonResponse
     */
    public function respondWithCollection($collection, $callback, string $message = 'Successfully', $extras = null): JsonResponse
    {
        $resource = new Collection($collection, $callback);

        $data = $this->fractal->createData($resource)->toArray();

        $data['count'] = count($data['data']);
        $data['message'] = $message;

        if($extras){
            $data['extras'] = $extras;
        }

        return $this->respondWithArray($data);
    }

    /**
     * Respond the collection data with pagination.
     *
     * @param $paginator
     * @param $callback
     * @param string $message
     * @return JsonResponse
     */
    public function respondWithPaginator($paginator, $callback, string $message = 'Successfully'): JsonResponse
    {
        $resource = new Collection($paginator->getCollection(), $callback);

        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        $data = $this->fractal->createData($resource)->toArray();
        $data['message'] = $message;

        return $this->respondWithArray($data);
    }

    /**
     * Respond the data.
     *
     * @param array $array
     * @param array $headers
     * @return JsonResponse
     */
    public function respondWithArray(array $array, array $headers = []): JsonResponse
    {
        return response()->json($array, $this->statusCode, $headers);
    }

    /**
     * Respond the message.
     *
     * @param string $message
     * @return JsonResponse
     */

    public function respondWithMessage(string $message): JsonResponse
    {
        return $this->setStatusCode(200)
            ->respondWithArray([
                'message' => $message,
            ]);
    }

    /**
     * Respond the error message.
     *
     * @param string $message
     * @param string $errorCode
     * @param array $errors
     * @return JsonResponse
     */
    protected function respondWithError(string $message, string $errorCode, array $errors = []): JsonResponse
    {
        if ($this->statusCode === 200) {
            trigger_error(
                "You better have a really good reason for erroring on a 200...",
                E_USER_WARNING
            );
        }

        return $this->respondWithArray([
            'errors' => $errors,
            'code' => $errorCode,
            'message' => $message,
        ]);
    }

    /**
     * Respond the error of 'Forbidden'
     *
     * @param string $message
     * @param array $errors
     * @return JsonResponse
     */
    public function errorForbidden(string $message = 'Forbidden', array $errors = []): JsonResponse
    {
        return $this->setStatusCode(500)
            ->respondWithError($message, self::CODE_FORBIDDEN, $errors);
    }

    /**
     * Respond the error of 'Internal Error'.
     *
     * @param string $message
     * @param array $errors
     * @return JsonResponse
     */
    public function errorInternalError(string $message = 'Internal Error', array $errors = []): JsonResponse
    {
        return $this->setStatusCode(500)
            ->respondWithError($message, self::CODE_INTERNAL_ERROR, $errors);
    }

    /**
     * Respond the error of 'Resource Not Found'
     *
     * @param string $message
     * @param array $errors
     * @return JsonResponse
     */
    public function errorNotFound(string $message = 'Resource Not Found', array $errors = []): JsonResponse
    {
        return $this->setStatusCode(404)
            ->respondWithError($message, self::CODE_NOT_FOUND, $errors);
    }

    /**
     * Respond the error of 'Unauthorized'.
     *
     * @param string $message
     * @param array $errors
     * @return JsonResponse
     */
    public function errorUnauthorized(string $message = 'Unauthorized', array $errors = []): JsonResponse
    {
        return $this->setStatusCode(401)
            ->respondWithError($message, self::CODE_UNAUTHORIZED, $errors);
    }

    /**
     * Respond the error of 'Wrong Arguments'.
     *
     * @param string $message
     * @param array $errors
     * @return JsonResponse
     */
    public function errorWrongArgs(string $message = 'Wrong Arguments', array $errors = []): JsonResponse
    {
        return $this->setStatusCode(400)
            ->respondWithError($message, self::CODE_WRONG_ARGS, $errors);
    }

}

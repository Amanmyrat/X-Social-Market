<?php

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ApiBaseController extends Controller
{
    protected int $statusCode = 200;

    const CODE_WRONG_ARGS = 'GEN-FUBARGS';

    const CODE_NOT_FOUND = 'GEN-LIKETHEWIND';

    const CODE_INTERNAL_ERROR = 'GEN-AAAGGH';

    const CODE_UNAUTHORIZED = 'GEN-MAYBGTFO';

    const CODE_FORBIDDEN = 'GEN-GTFO';

    protected Manager $fractal;

    public function __construct()
    {
        $this->fractal = new Manager;
        $this->fractal->setSerializer(new App\Helpers\FractalSerializer());

        //        $locale = $request->header('X-Localization') ?? 'tm';
        //        App::setLocale($locale);
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
     * @return $this
     */
    public function setStatusCode($statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Respond a no content response.
     */
    public function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Respond the item data.
     */
    public function respondWithItem($item, $callback, string $message = 'Successfully'): JsonResponse
    {
        $resource = new Item($item, $callback, 'data');

        $data = $this->fractal->createData($resource)->toArray();

        $data['message'] = $message;
        $data['success'] = true;

        return $this->respondWithArray($data);
    }

    /**
     * Respond the collection data.
     *
     * @param  null  $extras
     */
    public function respondWithCollection($collection, $callback, string $message = 'Successfully', $extras = null): JsonResponse
    {
        $resource = new Collection($collection, $callback, 'data');

        $data = $this->fractal->createData($resource)->toArray();

        $data['count'] = count($data['data']);
        $data['message'] = $message;

        if ($extras) {
            $data['extras'] = $extras;
        }

        return $this->respondWithArray($data);
    }

    /**
     * Respond the collection data with pagination.
     */
    public function respondWithPaginator($paginator, $callback, string $message = 'Successfully'): JsonResponse
    {
        $resource = new Collection($paginator->getCollection(), $callback, 'data');

        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        $data = $this->fractal->createData($resource)->toArray();
        $data['message'] = $message;

        return $this->respondWithArray($data);
    }

    /**
     * Respond the data.
     */
    public function respondWithArray(array $array, array $headers = []): JsonResponse
    {
        return response()->json($array, $this->statusCode, $headers);
    }

    /**
     * Respond the message.
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
     */
    protected function respondWithError(string $message, string $errorCode, array $errors = []): JsonResponse
    {
        if ($this->statusCode === 200) {
            trigger_error(
                'You better have a really good reason for erroring on a 200...',
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
     */
    public function errorForbidden(string $message = 'Forbidden', array $errors = []): JsonResponse
    {
        return $this->setStatusCode(500)
            ->respondWithError($message, self::CODE_FORBIDDEN, $errors);
    }

    /**
     * Respond the error of 'Internal Error'.
     */
    public function errorInternalError(string $message = 'Internal Error', array $errors = []): JsonResponse
    {
        return $this->setStatusCode(500)
            ->respondWithError($message, self::CODE_INTERNAL_ERROR, $errors);
    }

    /**
     * Respond the error of 'Resource Not Found'
     */
    public function errorNotFound(string $message = 'Resource Not Found', array $errors = []): JsonResponse
    {
        return $this->setStatusCode(404)
            ->respondWithError($message, self::CODE_NOT_FOUND, $errors);
    }

    /**
     * Respond the error of 'Unauthorized'.
     */
    public function errorUnauthorized(string $message = 'Unauthorized', array $errors = []): JsonResponse
    {
        return $this->setStatusCode(401)
            ->respondWithError($message, self::CODE_UNAUTHORIZED, $errors);
    }

    /**
     * Respond the error of 'Wrong Arguments'.
     */
    public function errorWrongArgs(string $message = 'Wrong Arguments', array $errors = []): JsonResponse
    {
        return $this->setStatusCode(400)
            ->respondWithError($message, self::CODE_WRONG_ARGS, $errors);
    }
}

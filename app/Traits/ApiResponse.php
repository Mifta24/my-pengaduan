<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /**
     * Success response with pagination
     */
    protected function successWithPagination($data, string $message = 'Data loaded successfully', array $filters = [], int $statusCode = 200, array $additionalData = []): JsonResponse
    {
        if ($data instanceof LengthAwarePaginator) {
            // Extract relative path from URL (remove domain)
            $nextPageUrl = $data->nextPageUrl();
            $prevPageUrl = $data->previousPageUrl();

            $nextPage = $nextPageUrl ? $this->extractRelativePath($nextPageUrl) : null;
            $prevPage = $prevPageUrl ? $this->extractRelativePath($prevPageUrl) : null;

            $meta = [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
                'has_next_page' => $data->hasMorePages(),
                'has_prev_page' => $data->currentPage() > 1,
                'next_page' => $nextPage,
                'prev_page' => $prevPage,
            ];

            // Add filters to meta if provided
            if (!empty($filters)) {
                $meta['filters'] = $filters;
            }

            $response = [
                'success' => true,
                'message' => $message,
                'meta' => $meta,
            ];

            // Add additional data if provided (e.g., statistics)
            if (!empty($additionalData)) {
                foreach ($additionalData as $key => $value) {
                    $response[$key] = $value;
                }
            }

            $response['data'] = $this->formatTimestamps($data->items());

            return response()->json($response, $statusCode);
        }

        return $this->success($data, $message, $statusCode);
    }    /**
     * Extract relative path from full URL
     */
    private function extractRelativePath(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $parsedUrl = parse_url($url);
        $relativePath = ($parsedUrl['path'] ?? '');

        if (isset($parsedUrl['query'])) {
            $relativePath .= '?' . $parsedUrl['query'];
        }

        return $relativePath;
    }

    /**
     * Format timestamps to ISO 8601 format (short)
     */
    private function formatTimestamps($data)
    {
        if (is_array($data) || $data instanceof \Illuminate\Support\Collection) {
            return collect($data)->map(function ($item) {
                return $this->formatItemTimestamps($item);
            })->toArray();
        }

        return $this->formatItemTimestamps($data);
    }

    /**
     * Format individual item timestamps
     */
    private function formatItemTimestamps($item)
    {
        if (is_object($item) && method_exists($item, 'toArray')) {
            $array = $item->toArray();

            // Format common timestamp fields
            foreach (['created_at', 'updated_at', 'deleted_at', 'resolved_at'] as $field) {
                if (isset($array[$field]) && $array[$field] instanceof \DateTime) {
                    $array[$field] = $array[$field]->format('Y-m-d\TH:i:s\Z');
                } elseif (isset($array[$field]) && is_string($array[$field])) {
                    try {
                        $date = new \DateTime($array[$field]);
                        $array[$field] = $date->format('Y-m-d\TH:i:s\Z');
                    } catch (\Exception $e) {
                        // Keep original value if parsing fails
                    }
                }
            }

            return $array;
        }

        return $item;
    }

    /**
     * Success response without pagination
     */
    protected function success($data, string $message = 'Operation successful', $metaOrStatusCode = 200, int $statusCode = 200): JsonResponse
    {
        // Handle backward compatibility
        // If $metaOrStatusCode is an integer, treat it as status code
        // If it's an array, treat it as meta data
        $meta = null;
        if (is_array($metaOrStatusCode)) {
            $meta = $metaOrStatusCode;
        } else {
            $statusCode = $metaOrStatusCode;
        }

        $response = [
            'success' => true,
            'message' => $message,
        ];

        // Add meta if provided
        if ($meta !== null) {
            $response['meta'] = $meta;
        }

        $response['data'] = $this->formatTimestamps($data);

        return response()->json($response, $statusCode);
    }

    /**
     * Success response for created resource
     */
    protected function created($data, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Success response for deleted resource
     */
    protected function deleted(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 200);
    }

    /**
     * Error response
     */
    protected function error(string $message, $errors = null, int $statusCode = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Validation error response
     */
    protected function validationError($errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->error($message, $errors, 422);
    }

    /**
     * Not found error response
     */
    protected function notFound(string $message = 'Data Not Found'): JsonResponse
    {
        return $this->error($message, null, 404);
    }

    /**
     * Unauthorized error response
     */
    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, null, 403);
    }

    /**
     * Server error response
     */
    protected function serverError(string $message = 'Server error occurred', $error = null): JsonResponse
    {
        $errors = null;

        if ($error && config('app.debug')) {
            $errors = [
                'message' => $error instanceof \Exception ? $error->getMessage() : $error,
                'file' => $error instanceof \Exception ? $error->getFile() : null,
                'line' => $error instanceof \Exception ? $error->getLine() : null,
            ];
        }

        return $this->error($message, $errors, 500);
    }
}

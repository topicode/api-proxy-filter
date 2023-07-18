<?php

namespace App\Requester;

use App\Requester\Response as RequesterResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RequestHandler
{
    protected const TTL = 60;

    public bool $enableCaching = true;

    public function handle(string $url, string $field): RequesterResponse
    {
        $data = null;
        if ($this->enableCaching) {
            $data = Cache::get($url);

            if ($data instanceof RequesterResponse) {
                return $data;
            }
        }

        if (!$data) {
            $httpResponse = Http::get($url);

            if ($httpResponse->status() !== 200) {
                return $this->saveResponse($url, 'Server returned error ' . $httpResponse->status(), $httpResponse->status());
            }

            $content = $httpResponse->toPsrResponse()->getBody() . '';
            $contentType = $httpResponse->header('content-type');
            if (empty($contentType)) {
                return $this->saveResponse($url, 'Got no parsable response', 400);
            }
            if (str_contains($contentType, 'json')) {
                $data = json_decode($content, true);
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                    return $this->saveResponse($url, 'Got invalid JSON response', 400);
                }
            } else {
                return $this->saveResponse($url, 'Got unknown response data type', 400);
            }

            if ($this->enableCaching) {
                Cache::set($url, $data, static::TTL);
            }
        }

        $value = Arr::get($data, $field, '[Value not found]');
        if (!is_scalar($value)) {
            return new RequesterResponse(
                '[Invalid field »' . $field . '«; value is not a scalar.]',
                400
            );
        }


        return new RequesterResponse('' . $value);
    }

    private function saveResponse(string $url, string $message, int $code): RequesterResponse
    {
        $response = new RequesterResponse('[' . $message . ']', $code);

        if ($this->enableCaching) {
            Cache::set($url, $response, static::TTL);
        }

        return $response;
    }
}

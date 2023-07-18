<?php

namespace App\Http\Controllers;

use App\Requester\RequestHandler;
use App\Rules\HostWhitelist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $url = $request->input('url');
        $field = $request->input('field');

        $validator = Validator::make([
            'url' => $url,
            'field' => $field,
        ], [
            'url' => ['required', 'url', new HostWhitelist(config('fetcher.url_whitelist') ?? [])],
            'field' => ['required', 'string'],
        ]);

        if (!$validator->passes()) {
            $errors = [];
            foreach ($validator->getMessageBag()->messages() as $key => $messages) {
                $errors[] = $key . ': ' . implode('; ', $messages);
            }
            return response('[Invalid request: ' . implode('; ', $errors) . ']', 400);
        }

        $requester = new RequestHandler();

        $response = $requester->handle($url, $field);
        return response($response->response, $response->status);
    }
}

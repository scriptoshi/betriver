<?php

namespace App\TwoFactorAuth\Http\Responses;

use Illuminate\Http\JsonResponse;

class TwoFactorEnabledResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', __('Two factor authentication enabled'));
    }
}

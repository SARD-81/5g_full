<?php

namespace Modules\SystemSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mews\Captcha\Facades\Captcha;

class CaptchaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getCaptcha(Request $request)
    {
        $captcha = Captcha::create('default', true);
        return response()->json([
            'image' => $captcha['img'],   // داده base64 تصویر
            'key' => $captcha['key'],     // شناسه کپچا
        ]);
    }

}

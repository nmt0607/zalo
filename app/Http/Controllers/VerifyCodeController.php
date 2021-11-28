<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountAlreadyActiveException;
use App\Exceptions\CantGetVerifyCodeException;
use App\Exceptions\CantVerifyCodeException;
use App\Exceptions\UserNotValidatedException;
use App\Exceptions\VerifyCodeIncorrectException;
use App\Http\Requests\CheckVerifyCodeRequest;
use App\Http\Requests\GetVerifyCodeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Throwable;
use Twilio\Rest\Client;

class VerifyCodeController extends Controller
{
    /**
     * @var twilio_verification_sid
     */
    protected $twilio_verification_sid;

    public function get_verify_code(GetVerifyCodeRequest $request)
    {
        $phonenumber = $request->phonenumber;
        $phone = '+84' . substr($phonenumber, -9);

        $user = User::where('phonenumber', $phonenumber)->first();
        if (!$user) {
            throw new UserNotValidatedException();
        }

        if ($user->state=='active') {
            throw new AccountAlreadyActiveException();
        }

        try {
            $sid = getenv("TWILIO_ACCOUNT_SID");
            $token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_verification_sid = getenv("TWILIO_VERIFICATION_SID");
            $twilio = new Client($sid, $token);
            $verification = $twilio->verify->v2->services($twilio_verification_sid)
                ->verifications
                ->create($phone, "sms");
        } catch (Throwable $e) {
            throw new CantGetVerifyCodeException();
        }

        if ($verification->status == 'pending') {
            return [
                'code' => config('response_code.ok'),
                'message' => __('messages.ok'),
            ];
        } else {
            throw new CantGetVerifyCodeException();
        }
    }

    public function check_verify_code(CheckVerifyCodeRequest $request)
    {
        $phonenumber = $request->phonenumber;
        $phone = '+84' . substr($phonenumber, -9);
        $code_verify = $request->code_verify;

        $user = User::where('phonenumber', $phonenumber)->first();
        if (!$user) {
            throw new UserNotValidatedException();
        }

        if ($user->state=='active') {
            throw new AccountAlreadyActiveException();
        }

        try {
            $sid = getenv("TWILIO_ACCOUNT_SID");
            $token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_verification_sid = getenv("TWILIO_VERIFICATION_SID");
            $twilio = new Client($sid, $token);
            $verification_check = $twilio->verify->v2->services($twilio_verification_sid)
                ->verificationChecks
                ->create(
                    $code_verify,
                    ["to" => $phone]
                );
        } catch (Throwable $e) {
            throw new CantVerifyCodeException();
        }

        if ($verification_check->status == 'approved') {
            $user->state = 'active';
            $user->save();
            $user->tokens()->delete();
            $token = $user->createToken('myapptoken')->plainTextToken;
            return [
                'code' => config('response_code.ok'),
                'message' => __('messages.ok'),
                'data' => [
                    'token' => $token,
                    'id' => $user->id,
                    'active' => 'active',
                ]
            ];
        }
        elseif ($verification_check->status == 'pending') {
            throw new VerifyCodeIncorrectException();
        }
        else {
            return $verification_check->status;
            throw new CantVerifyCodeException();
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * The user has been registered.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    protected function registered(Request $request, User $user)
    {
        if ($user instanceof MustVerifyEmail) {
            return response()->json(['status' => trans('verification.sent')]);
        }

        return response()->json($user);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email:filter|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|numeric|unique:users',
            'valid_id' => 'nullable|string',
            'ref' => 'nullable|string',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $referralCode = Str::random(8);

        $accountNo = $this->generateAccountNumber();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone' => $data['phone'],
            'referral_code' => $referralCode,
            'account_no' => $accountNo,
            'valid_id' => data_get($data, 'valid_id'),
            'kyc_level' => data_get($data, 'valid_id') ? User::LEVEL_2 : User::LEVEL_1,
        ]);

        $referral = data_get($data, 'ref');

        if ($referral) {
            User::whereReferralCode($referral)->increment('balance', User::REFERRAL_BONUS);
        }

        return $user;
    }

    protected function generateAccountNumber() {
        $number = mt_rand(1000000000, 9999999999); // better than rand()

        // call the same function if the barcode exists already
        if ($this->accountNumberExists($number)) {
            return $this->generateAccountNumber();
        }

        // otherwise, it's valid and can be used
        return $number;
    }

    protected function accountNumberExists($number) {
        // query the database and return a boolean
        // for instance, it might look like this in Laravel
        return User::whereAccountNo($number)->exists();
    }
}

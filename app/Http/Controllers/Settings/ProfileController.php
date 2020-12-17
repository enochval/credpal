<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Jobs\TransferFundJob;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'valid_id' => 'required|string',
        ]);

        return tap($user)->update([
            'valid_id' => $request->valid_id,
            'kyc_level' => User::LEVEL_2,
        ]);
    }

    public function transferFund(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'transfer_type' => 'required|string',
            'amount' => 'required|numeric',
            'future_date' => 'required_if:transfer_type,future|nullable|date',
            'account_no' => 'required|digits:10',
        ]);

        $amount = $request->amount;
        $accountNo = $request->account_no;

        if (!$receiver = User::where('account_no', $accountNo)->first()) {
            throw new \Exception("Can not resolve account number to any user", 400);
        }

        if ($amount > $user->balance) {
            throw new \Exception("You have insufficient balance for this transaction", 400);
        }

        if ($user->kyc_level == User::LEVEL_1 && $amount > 50000) {
            throw new \Exception("You have reached your transfer limit for your KYC level", 400);
        }

        if ($accountNo == $user->account_no) {
            throw new \Exception("Cannot transfer fund to self", 400);
        }

        if (strtolower($request->transfer_type) == 'instant') {
            $user->decrement('balance', $amount);
            $receiver->increment('balance', $amount);
        } else {

            $futureDate = Carbon::parse($request->future_date)->toDateString();

            if ($futureDate <= Carbon::now()->toDateString()) {
                throw new \Exception("The date has to be a date from tomorrow and above", 400);
            }

            $diff = Carbon::now()->diffInHours($futureDate);

            TransferFundJob::dispatch($user, $amount, $accountNo)
                ->delay(now()->addHours($diff));
        }

        return $user;
    }
}

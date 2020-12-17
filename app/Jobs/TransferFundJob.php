<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransferFundJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $amount;
    private $accountNo;

    /**
     * Create a new job instance.
     *
     * @param $user
     * @param $amount
     * @param $accountNo
     */
    public function __construct($user, $amount, $accountNo)
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->accountNo = $accountNo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->decrement('balance', $this->amount);
        User::whereAccountNo($this->accountNo)->increment('balance', $this->amount);
    }
}

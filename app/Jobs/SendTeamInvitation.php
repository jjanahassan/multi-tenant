<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Invitation;

class SendTeamInvitation implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Invitation $invitation
    )
    {
        
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}

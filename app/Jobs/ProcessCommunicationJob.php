<?php

namespace App\Jobs;

use App\Services\CommunicationProcessService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCommunicationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly string $communicationId)
    {
        $this->onQueue('communications');
    }

    public function handle(CommunicationProcessService $communicationProcessService): void
    {
        $communicationProcessService->process($this->communicationId, $this->attempts());
    }
}

<?php

namespace App\Services;

use App\DataTransferObjects\CommunicationDTO;
use App\Enums\LogLevelEnum;
use App\Helpers\LogHelper;
use App\Jobs\ProcessCommunicationJob;
use App\Models\Communication;

class CommunicationService
{
    public function __construct(
        private readonly Communication $model,
        private readonly LogHelper $logHelper,
    ) {}

    public function createAndDispatch(CommunicationDTO $communication): Communication
    {
        $communication = $this->model->create($communication->toArray());
        ProcessCommunicationJob::dispatch($communication->id);

        $this->logHelper->create(
            communicationId: $communication->id,
            level: LogLevelEnum::INFO,
            message: 'Communication request created.',
            context: ['communication' => $communication->toArray()],
        );

        return $communication;
    }
}

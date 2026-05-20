<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\CommunicationDTO;
use App\Enums\LogLevelEnum;
use App\Helpers\LogHelper;
use App\Http\Requests\CommunicationRequest;
use App\Services\CommunicationService;
use Illuminate\Http\JsonResponse;

class CommunicationController extends Controller
{
    public function __construct(
        private readonly CommunicationService $communicationService,
        private readonly LogHelper $logHelper,
    ) {}

    public function store(CommunicationRequest $request): JsonResponse
    {
        try {
            $communicationDTO = CommunicationDTO::fromArray($request->validated());
            $communication = $this->communicationService->createAndDispatch($communicationDTO);

            return response()->json(
                [
                    'message' => 'Communication request received and is being processed.',
                    'communication' => $communication->toArray(),
                ], 202);

        } catch (\Exception $e) {
            $this->logHelper->create(
                communicationId: null,
                level: LogLevelEnum::ERROR,
                message: 'Failed to process communication request: '.$e->getMessage(),
                context: ['error' => $e->getMessage()],
            );

            return response()->json(['error' => 'Failed to process communication request.'], 500);
        }

    }
}

<?php

namespace App\Http\Requests;

use App\Enums\ChannelEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommunicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient' => ['required', 'string', 'email'],
            'channel' => ['required', 'string', Rule::enum(ChannelEnum::class)],
            'subject' => ['string'],
            'message' => ['required', 'string'],
            'origin_system' => ['required', 'string'],
        ];
    }
}

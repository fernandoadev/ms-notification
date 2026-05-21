<?php

namespace Database\Factories;

use App\Enums\ChannelEnum;
use App\Enums\CommunicationStatusEnum;
use App\Models\Communication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Communication>
 */
class CommunicationFactory extends Factory
{
    protected $model = Communication::class;

    public function definition(): array
    {
        return [
            'recipient' => fake()->safeEmail(),
            'channel' => fake()->randomElement(ChannelEnum::cases())->value,
            'subject' => fake()->optional()->sentence(),
            'message' => fake()->paragraph(),
            'origin_system' => fake()->slug(2),
            'status' => CommunicationStatusEnum::CREATED->value,
            'attempts' => 0,
        ];
    }
}

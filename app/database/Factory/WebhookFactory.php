<?php

declare(strict_types=1);

namespace Database\Factory;

use App\Application\Domain\ValueObjects\Uuid;
use Modules\Webhooks\Domain\ValueObject\Url;
use Modules\Webhooks\Domain\Webhook;
use Spiral\DatabaseSeeder\Factory\AbstractFactory;

/**
 * @template TEntity of Webhook
 * @extends AbstractFactory<Webhook>
 */
final class WebhookFactory extends AbstractFactory
{
    public function makeEntity(array $definition): object
    {
        $uuid = $definition['uuid'] ?? Uuid::generate();

        return new Webhook(
            uuid: $uuid,
            key: $definition['key'],
            event: $definition['event'],
            url: $definition['url'],
            headers: $definition['headers'],
            verifySsl: $definition['verifySsl'],
            retryOnFailure: $definition['retryOnFailure'],
        );
    }

    public function entity(): string
    {
        return Webhook::class;
    }

    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->slug(2),
            'event' => $this->faker->randomElement(['user.created', 'user.updated', 'user.deleted']),
            'url' => Url::create($this->faker->url()),
            'headers' => [],
            'verifySsl' => $this->faker->boolean(),
            'retryOnFailure' => $this->faker->boolean(),
        ];
    }
}

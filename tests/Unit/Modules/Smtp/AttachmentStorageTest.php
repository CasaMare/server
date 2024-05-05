<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Smtp;

use App\Application\Domain\ValueObjects\Uuid;
use Database\Factory\AttachmentFactory;
use Mockery\MockInterface;
use Modules\Smtp\Application\Mail\Attachment;
use Modules\Smtp\Application\Storage\AttachmentStorage;
use Modules\Smtp\Domain\AttachmentFactoryInterface;
use Modules\Smtp\Domain\AttachmentRepositoryInterface;
use Spiral\Storage\BucketInterface;
use Tests\TestCase;

final class AttachmentStorageTest extends TestCase
{
    private AttachmentStorage $storage;
    private MockInterface|BucketInterface $bucket;
    private MockInterface|AttachmentRepositoryInterface $attachments;
    private MockInterface|AttachmentFactoryInterface $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = new AttachmentStorage(
            $this->bucket = $this->mockContainer(BucketInterface::class),
            $this->attachments = $this->mockContainer(AttachmentRepositoryInterface::class),
            $this->factory = $this->mockContainer(AttachmentFactoryInterface::class),
        );
    }

    public function testStore(): void
    {
        $attachment1 = new Attachment(
            filename: 'file1.txt',
            content: 'Hello, world!',
            type: 'text/plain',
        );

        $attachment2 = new Attachment(
            filename: 'image.png',
            content: 'image content',
            type: 'image/png',
        );

        $eventUuid = Uuid::generate();

        $this->bucket->shouldReceive('write')
            ->with($path1 = $eventUuid . '/file1.txt', 'Hello, world!')
            ->once()
            ->andReturn($file1 = $this->mockContainer(\Spiral\Storage\FileInterface::class));

        $file1->shouldReceive('getPathname')->andReturn($path1);
        $file1->shouldReceive('getSize')->andReturn(12);
        $file1->shouldReceive('getMimeType')->andReturn('text/plain');

        $this->factory->shouldReceive('create')
            ->with(
                $eventUuid,
                $attachment1->getFilename(),
                $path1,
                12,
                'text/plain',
                $attachment1->getId(),
            )
            ->once()
            ->andReturn($entity1 = $this->mockContainer(\Modules\Smtp\Domain\Attachment::class));

        $this->bucket->shouldReceive('write')
            ->with($path2 = $eventUuid . '/image.png', 'image content')
            ->once()
            ->andReturn($file2 = $this->mockContainer(\Spiral\Storage\FileInterface::class));

        $file2->shouldReceive('getPathname')->andReturn($path2);
        $file2->shouldReceive('getSize')->andReturn(14);
        $file2->shouldReceive('getMimeType')->andReturn('image/png');

        $this->factory->shouldReceive('create')
            ->with(
                $eventUuid,
                $attachment2->getFilename(),
                $path2,
                14,
                'image/png',
                $attachment2->getId(),
            )
            ->once()
            ->andReturn($entity2 = $this->mockContainer(\Modules\Smtp\Domain\Attachment::class));

        $this->attachments->shouldReceive('store')->with($entity1)->once();
        $this->attachments->shouldReceive('store')->with($entity2)->once();

        $this->storage->store($eventUuid, [$attachment1, $attachment2]);
    }

    public function testRemove(): void
    {
        $eventUuid = Uuid::generate();

        $attachments = AttachmentFactory::new()->forEvent($eventUuid)->times(3)->make();

        $this->attachments->shouldReceive('findByEvent')
            ->with($eventUuid)
            ->once()
            ->andReturn($attachments);

        foreach ($attachments as $attachment) {
            $this->bucket->shouldReceive('delete')->once()->with($attachment->getPath(), true);
        }

        $this->storage->deleteByEvent($eventUuid);
    }
}

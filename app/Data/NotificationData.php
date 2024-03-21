<?php

namespace App\Data;

use Spatie\LaravelData\Data;

/** @typescript */
class NotificationData extends Data
{
    public function __construct(
        public string $id,
        public bool $read,
        public string $dateTime,
        public string $title,
        public string $body,
        public string $link,
        public string $icon,
    ) {
    }

    public static function fromModel(mixed $notification): self
    {
        return new self(
            $notification->id,
            (bool) $notification->read_at,
            $notification->created_at->shortRelativeDiffForHumans(),
            $notification->data['title'],
            $notification->data['body'],
            config('app.frontend_url').$notification->data['link'],
            $notification->data['icon'],
        );
    }
}

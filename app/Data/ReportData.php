<?php

namespace App\Data;

use App\Models\Report;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;

/** @typescript */
class ReportData extends Data
{
    public function __construct(
        #[MapInputName('uuid')]
        public string $id,
        #[Rule(['required', 'string', 'min:5', 'max:255'])]
        public string $reason,
        public Lazy|Optional|StaffData $staff,
        public Lazy|IdeaData|CommentData $target,
        public string $reportedAt,
    ) {
    }

    public static function fromModel(Report $report): self
    {
        return new self(
            $report->uuid,
            $report->reason,
            Lazy::create(fn () => StaffData::from($report->staff)->only('id', 'name')),
            Lazy::create(function () use ($report) {
                $dataInfo = match ($report->reportable->getMorphClass()) {
                    'idea' => [
                        'class' => IdeaData::class,
                        'only' => '{title, content}'
                    ],
                    'comment' => [
                        'class' => CommentData::class,
                        'only' => 'content'
                    ],
                };

                return $dataInfo['class']::from($report->reportable)->only($dataInfo['only']);
            }),
            $report->created_at->format('Y-m-d H:i:s'),
        );
    }
}

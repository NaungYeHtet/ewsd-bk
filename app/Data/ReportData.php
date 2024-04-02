<?php

namespace App\Data;

use App\Models\Report;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

/** @typescript */
class ReportData extends Data
{
    public function __construct(
        #[MapInputName('uuid')]
        public string $id,
        #[Rule(['required', 'string', 'min:5', 'max:255'])]
        public string $reason,
        public Lazy|IdeaData|CommentData|array $target,
        public Lazy|StaffData $reportedBy,
        public Lazy|StaffData|null $reportedTo,
        public ?string $actionAt,
        public string $reportedAt,
    ) {
    }

    public static function fromModel(Report $report): self
    {
        return new self(
            $report->uuid,
            $report->reason,
            Lazy::create(function () use ($report) {
                $dataInfo = match ($report->reportable()->getRelated()->getMorphClass()) {
                    'idea' => [
                        'class' => IdeaData::class,
                        'only' => '{type, title, content}',
                    ],
                    'comment' => [
                        'class' => CommentData::class,
                        'only' => '{type, content}',
                    ],
                };

                if($report->reportable){
                    return $dataInfo['class']::from($report->reportable)->only($dataInfo['only']);
                }

                return [
                    'type' => $report->reportable()->getRelated()->getMorphClass(),
                ];
            }),
            Lazy::create(fn () => StaffData::from($report->staff)->only('id', 'name')),
            Lazy::create(fn () => $report->reportable ? StaffData::from($report->reportable->staff)->only('id', 'name') : null),
            $report->action_at ? $report->action_at->format('Y-m-d H:i:s') : null,
            $report->created_at->format('Y-m-d H:i:s'),
        );
    }
}

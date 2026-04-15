<?php

namespace App\Events;

use App\Models\Absence;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AbsenceSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Absence $absence;

    /**
     * Create a new event instance.
     */
    public function __construct(Absence $absence)
    {
        $this->absence = $absence;
    }
}

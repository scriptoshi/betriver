<?php

namespace App\Events;

use App\Http\Resources\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    private $channel;
    /**
     * Create a new event instance.
     */
    public function __construct(public Game $game)
    {
        //
        $this->channel = $this->game->id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [new Channel("gamemarket.$this->channel")];
    }
}

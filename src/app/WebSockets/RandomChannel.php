<?php

declare(strict_types=1);

namespace App\WebSockets;

use Libxa\WebSockets\WsChannel;
use Libxa\WebSockets\WsConnection;
use Libxa\WebSockets\Attributes\WsRoute;
use Libxa\WebSockets\Attributes\OnEvent;

#[WsRoute('/ws/random')]
class RandomChannel extends WsChannel
{
    public function onOpen(WsConnection $connection): void
    {
        // Join the global random number stream room
        $connection->join('random-stream');
        
        $connection->send([
            'event' => 'connected',
            'message' => 'You are now receiving random numbers from the server!'
        ]);
    }

    #[OnEvent('ping')]
    public function handlePing(WsConnection $connection): void
    {
        $connection->send(['event' => 'pong', 'time' => time()]);
    }
}

<?php

namespace Server;

use Ratchet\ConnectionInterface;

class CustomIoConnection implements ConnectionInterface
{
    public $decor;
    public $resourceId;
    public $remoteAddress;
    public $httpHeadersReceived;
    public $httpBuffer;
    public $httpRequest;
    public $WebSocket;

    public function __construct(ConnectionInterface $decor)
    {
        $this->decor = $decor;
        $this->resourceId = null;
        $this->remoteAddress = null;
        $this->httpHeadersReceived = null;
        $this->httpBuffer = null;
        $this->httpRequest = null;
        $this->WebSocket = null;
    }

    public function send($data)
    {
        $this->decor->send($data);
    }

    public function close()
    {
        $this->decor->close();
    }
}

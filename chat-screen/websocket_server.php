<?php
require 'vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;

class ChatServer implements \Ratchet\MessageComponentInterface {
    protected $clients;
    protected $users;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->users = [];
    }

    public function onOpen(\Ratchet\ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(\Ratchet\ConnectionInterface $from, $msg) {
        $data = json_decode($msg);
        
        switch($data->type) {
            case 'register':
                $this->users[$from->resourceId] = $data->username;
                $this->broadcastUserList();
                break;
                
            case 'message':
                $this->sendMessage($from, $data->to, $data->message);
                break;
                
            case 'typing':
                $this->sendTypingStatus($from, $data->to, $data->isTyping);
                break;
        }
    }

    public function onClose(\Ratchet\ConnectionInterface $conn) {
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        $this->broadcastUserList();
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    protected function broadcastUserList() {
        $userList = array_values($this->users);
        $message = json_encode([
            'type' => 'userList',
            'users' => $userList
        ]);

        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }

    protected function sendMessage($from, $to, $message) {
        $fromUsername = $this->users[$from->resourceId];
        $messageData = json_encode([
            'type' => 'message',
            'from' => $fromUsername,
            'message' => $message
        ]);

        foreach ($this->clients as $client) {
            if (isset($this->users[$client->resourceId]) && 
                ($this->users[$client->resourceId] === $to || $client === $from)) {
                $client->send($messageData);
            }
        }
    }

    protected function sendTypingStatus($from, $to, $isTyping) {
        $fromUsername = $this->users[$from->resourceId];
        $typingData = json_encode([
            'type' => 'typing',
            'from' => $fromUsername,
            'isTyping' => $isTyping
        ]);

        foreach ($this->clients as $client) {
            if (isset($this->users[$client->resourceId]) && 
                $this->users[$client->resourceId] === $to) {
                $client->send($typingData);
            }
        }
    }
}

$loop = Factory::create();
$webSocket = new ChatServer();
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            $webSocket
        )
    ),
    8080
);

echo "WebSocket server started on port 8080\n";
$server->run(); 
<?php
/**
 * Created by PhpStorm.
 * User: shebinleovincent
 * Date: 21/3/15
 * Time: 2:36 PM
 */

namespace Chat;


use Evenement\EventEmitterInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class Chat implements ChatInterface
{

  protected $users;
  protected $emitter;
  protected $id = 1;

  public function getUserBySocket(ConnectionInterface $socket)
  {
    foreach ($this->users as $next) {
      if ($next->getSocket() === $socket) {
        return $next;
      }
    }
    return null;
  }

  public function getEmitter()
  {
    return $this->emitter;
  }

  public function setEmitter(EventEmitterInterface $emitter)
  {
    $this->emitter = $emitter;
  }

  public function getUsers()
  {
    return $this->users;
  }

  public function __construct(EventEmitterInterface $emitter)
  {
    $this->emitter = $emitter;
    $this->users = new SplObjectStorage();
  }

  /**
   * When a new connection is opened it will be passed to this method
   * @param  ConnectionInterface $conn The socket/connection that just connected to your application
   * @throws \Exception
   */
  function onOpen(ConnectionInterface $socket)
  {
    $user = new User();
    $user->setId($this->id++);
    $user->setSocket($socket);
    $this->users->attach($user);
    $this->emitter->emit("open", [$user]);
    $socket->send(json_encode([
      "user" => [
        "id" => $user->getId(),
        "name" => $user->getName()
      ],
      "message" => ["type" => "user"]
    ]));
  }

  /**
   * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
   * @param  ConnectionInterface $conn The socket/connection that is closing/closed
   * @throws \Exception
   */
  function onClose(ConnectionInterface $socket)
  {
    $user = $this->getUserBySocket($socket);
    if ($user) {
      $this->users->detach($user);
      $this->emitter->emit("close", [$user]);
    }
  }

  /**
   * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
   * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
   * @param  ConnectionInterface $conn
   * @param  \Exception $e
   * @throws \Exception
   */
  function onError(ConnectionInterface $socket, \Exception $exception)
  {
    $user = $this->getUserBySocket($socket);
    if ($user) {
      $user->getSocket()->close();
      $this->emitter->emit("error", [$user, $exception]);
    }
  }

  /**
   * Triggered when a client sends data through the socket
   * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
   * @param  string $msg The message received
   * @throws \Exception
   */
  function onMessage(ConnectionInterface $socket, $message)
  {
    $user = $this->getUserBySocket($socket);
    $message = json_decode($message);
    switch ($message->type) {
      case "name": {
        $user->setName($message->data);
        $this->emitter->emit("name", [
          $user,
          $message->data
        ]);
        break;
      }
      case "message": {
        $this->emitter->emit("message", [
          $user,
          $message->data
        ]);
        break;
      }
    }
    foreach ($this->users as $next) {
      if ($next !== $user) {
        $next->getSocket()->send(json_encode([
          "user" => [
            "id" => $user->getId(),
            "name" => $user->getName()
          ],
          "message" => $message
        ]));
      }
    }
  }
}
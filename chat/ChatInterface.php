<?php
/**
 * Created by PhpStorm.
 * User: shebinleovincent
 * Date: 21/3/15
 * Time: 2:41 PM
 */

namespace Chat;

use Evenement\EventEmitterInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

interface ChatInterface extends MessageComponentInterface
{
  public function getUserBySocket(ConnectionInterface $socket);

  public function getEmitter();

  public function setEmitter(EventEmitterInterface $emitter);

  public function getUsers();
}
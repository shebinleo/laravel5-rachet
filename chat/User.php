<?php
/**
 * Created by PhpStorm.
 * User: shebinleovincent
 * Date: 21/3/15
 * Time: 3:20 PM
 */

namespace Chat;


use Ratchet\ConnectionInterface;

class User implements UserInterface
{
  protected $socket;
  protected $id;
  protected $name;

  public function getSocket()
  {
    return $this->socket;
  }

  public function setSocket(ConnectionInterface $socket)
  {
    $this->socket = $socket;
    return $this;
  }

  public function getId()
  {
    return $this->id;
  }

  public function setId($id)
  {
    $this->id = $id;
    return $this;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
    return $this;
  }
}
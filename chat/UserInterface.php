<?php
/**
 * Created by PhpStorm.
 * User: shebinleovincent
 * Date: 21/3/15
 * Time: 3:17 PM
 */

namespace Chat;

use Ratchet\ConnectionInterface;

interface UserInterface
{
  public function getSocket();

  public function setSocket(ConnectionInterface $socket);

  public function getId();

  public function setId($id);

  public function getName();

  public function setName($name);
}
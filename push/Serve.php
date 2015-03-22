<?php
/**
 * Created by PhpStorm.
 * User: shebinleovincent
 * Date: 22/3/15
 * Time: 8:50 PM
 */

namespace Push;


use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\ZMQ\Context;
use Symfony\Component\Console\Input\InputOption;
use ZMQ;


class Serve extends Command
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'push:serve';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'boot wamp server.';

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function fire()
  {
    $port = (integer)$this->option("port");
    if (!$port) {
      $port = 7778;
    }

    $loop   = Factory::create();
    $pusher = new Pusher;

    $context = new Context($loop);
    $pull = $context->getSocket(ZMQ::SOCKET_PULL);
    $pull->bind('tcp://127.0.0.1:5555');
    $pull->on('message', array($pusher, 'onBlogEntry'));

    $webSock = new Server($loop);
    $webSock->listen($port, '0.0.0.0');
    $webServer = new IoServer(
      new HttpServer(
        new WsServer(
          new WampServer($pusher)
        )
      ), $webSock);
    $this->info('wamp boot');
    $this->info('Listening on port ' . $port);
    $loop->run();
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      [
        "port",
        null,
        InputOption::VALUE_REQUIRED,
        "Port to listen on.",
        null
      ]
    ];
  }

}
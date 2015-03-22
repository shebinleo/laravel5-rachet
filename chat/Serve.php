<?php namespace Chat;


use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Input\InputOption;


class Serve extends Command
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'chat:serve';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description.';

  protected $chat;

  protected function getUserName($user)
  {
    $suffix = " (" . $user->getId() . ")";
    if ($name = $user->getName())
    {
      return $name . $suffix;
    }
    return "User" . $suffix;
  }

  /**
   * Create a new command instance.
   *
   * @param ChatInterface $chat
   */
  public function __construct(ChatInterface $chat)
  {
    parent::__construct();

    $this->chat = $chat;

    $open = function(UserInterface $user)
    {
      $name = $this->getUserName($user);
      $this->line("<info>" . $name . " connected.</info>");
    };
    $this->chat->getEmitter()->on("open", $open);
    $close = function(UserInterface $user)
    {
      $name = $this->getUserName($user);
      $this->line(" <info>" . $name . " disconnected.</info>");
    };
    $this->chat->getEmitter()->on("close", $close);
    $message = function(UserInterface $user, $message)
    {
      $name = $this->getUserName($user);
      $this->line("<info>New message from " . $name . ":</info><comment>" . $message . "</comment><info>.</info>");
    };
    $this->chat->getEmitter()->on("message", $message);
    $name = function(UserInterface $user, $message)
    {
      $this->line("<info>User changed their name to:</info><comment>" . $message . "</comment><info>.</info>");
    };
    $this->chat->getEmitter()->on("name", $name);
    $error = function(UserInterface $user, $exception)
    {
      $message = $exception->getMessage();
      $this->line("<info>User encountered an exception:</info><comment>" . $message . "</comment><info>.</info>");
    };
    $this->chat->getEmitter()->on("error", $error);
  }

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
    $server = IoServer::factory(
      new HttpServer(
        new WsServer(
          $this->chat
        )
      ),
      $port
    );
    $this->line("<info>Listening on port <comment>".$port."</comment>.</info>");
    $server->run();
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

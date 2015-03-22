<?php namespace Chat;

use Chat\Serve;
use Chat\Chat;
use Chat\User;
use Evenement\EventEmitter;
use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = true;

  /**
   * Register the application services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->bind('Chat\UserInterface', 'Chat\User');
    $this->app->bind('Chat\ChatInterface', 'Chat\Chat');
    $this->app->bind('Evenement\EventEmitterInterface', 'Evenement\EventEmitter');
    $this->app->bind("chat.emitter", function () {
      return new EventEmitter();
    });
    $this->app->bind("chat.chat",
      function () {
        return new Chat(
          $this->app->make("chat.emitter")
        );
      });
    $this->app->bind("chat.user", function () {
      return new User();
    });
    $this->app->bind("chat.command.serve", function () {
      return new Serve(
        $this->app->make("chat.chat")
      );
    });
    $this->commands("chat.command.serve");
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return [
      "chat.chat",
      "chat.command.serve",
      "chat.emitter",
      "chat.server"
    ];
  }

}

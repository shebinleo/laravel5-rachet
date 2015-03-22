<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Faker\Factory;
use Illuminate\Http\Request;
use ZMQ;
use ZMQContext;

class ChatController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
    return view('chat');
	}

  public function create() {
    return view('push');
  }

  public function store() {
    $faker = Factory::create();
    $entryData = [
      'cat'     => 'news',
      'title'   => $faker->sentence(),
      'article' => $faker->paragraph(20),
      'when'    => time()
    ];
    $context = new ZMQContext();
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher1');
    $socket->connect("tcp://127.0.0.1:5555");
    $socket->send(json_encode($entryData));
  }

}

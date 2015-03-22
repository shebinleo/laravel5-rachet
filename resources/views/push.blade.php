@extends('app')

@section('content')
  <h1>Blog&nbsp;&nbsp;&nbsp;<button class="btn btn-primary" onclick="$.get('/publish');">Publish</button></h1>
  <div class="welcome"></div>
@endsection

@section('script')
  <script src="http://autobahn.s3.amazonaws.com/js/autobahn.min.js"></script>
  <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
  <script>
    var conn = new ab.Session(
        'ws://127.0.0.1:7778' , function() {
          conn.subscribe('news', function(topic, data) {
            console.log('New article published to category "' + topic + '" : ' + data.title);
            $('.welcome').prepend(
                '<div>New article published to category "' + topic + '" <em>' + new Date(data.when*1000) + '</em>'
                + '<h3>' + data.title + '</h3>'
                + "<p>" + data.article + '</p>'
                + '<hr></div>'
            );
          });
        }, function() {
          console.warn('WebSocket connection closed');
        }, {
          'skipSubprotocolCheck': true
        }
    );
  </script>
@endsection
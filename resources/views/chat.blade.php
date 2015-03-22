@extends('app')

@section('content')

  <div class="container">
    <div class="row">
      <h1>Username: <span id="username"></span></h1>
    </div>
  </div>
  <script type="text/x-handlebars">
    @{{outlet}}

  </script>
  <script type="text/x-handlebars" data-template-name="index">

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped">
                    @{{#each message in model}}
                        <tr>
                            <td @{{bind-attr class="message.user_id_class"}}>
                                @{{message.user_name}}
                            </td>
                            <td>
                                @{{message.message}}
                            </td>
                        </tr>
                    @{{/each}}
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="input-group">
                    @{{input
                        type="text"
                        value=command
                        class="form-control"
                    }}
                    <span class="input-group-btn">
                        <button
                            class="btn btn-default"
                            @{{action "send"}}
                        >
                            Send
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>

  </script>

@endsection

@section('script')
  <script type="text/javascript" src="{{ asset("js/handlebars.1.0.0.js") }}"></script>
  <script type="text/javascript" src="{{ asset("js/ember.1.1.1.js") }}"></script>
  <script type="text/javascript" src="{{ asset("js/ember.data.1.0.0.js") }}"></script>
  <script type="text/javascript" src="{{ asset("js/shared.js") }}"></script>
@endsection
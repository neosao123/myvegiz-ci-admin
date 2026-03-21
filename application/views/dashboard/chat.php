<div class="container">
  <div class="row">
    <div class="col-12 mt-5 pt-3 pb-3 bg-white from-wrapper">
      <div class="container">
        <h3>Chat</h3>
        <hr>
        <div class="row">
          <div class="col-12 col-sm-12 col-md-4 mb-3">
            <ul id="user-list" class="list-group"></ul>
          </div>
          <div class="col-12 col-sm-12 col-md-8">
            <div class="row">
              <div class="col-12">
                <div class="message-holder">
                    <div id="messages" class="row"></div>
                </div>
                <div class="form-group">
                 <textarea id="message-input" class="form-control" name="" rows="2"></textarea>
                </div>
            </div>
              <div class="col-12">
                <button id="send" class="btn float-right  btn-primary">Send</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
var conn = new WebSocket('wss://162.240.44.23:8282');
    var client = {
        user_id: 12,
        recipient_id: null,
        type: 'socket',
        token: null,
        message: null
    };

    conn.onopen = function (e) {
        conn.send(JSON.stringify(client));
        $('#messages').append('<font color="green">Successfully connected as user ' + client.user_id + '</font><br>');
    };

    conn.onmessage = function (e) {
        var data = JSON.parse(e.data);
        if (data.message) {
            $('#messages').append(data.user_id + ' : ' + data.message + '<br>');
        }
        if (data.type === 'token') {
            $('#token').html('JWT Token : ' + data.token);
        }
    };

    $('#submit').click(function () {
        client.message = $('#text').val();
        client.token = $('#token').text().split(': ')[1];
        client.type = 'chat';
        if ($('#recipient_id').val()) {
            client.recipient_id = $('#recipient_id').val();
        }
        conn.send(JSON.stringify(client));
    });
</script>
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   :  [HP-Union]
 * Date   : 2018-03-01
 * Time   : 23:39
 */
 
 
$(document).ready(function (){

  $(".gate").css('height', $(".gate").css('width'));
  $(".gate").css('line-height', 'calc(' + $(".gate").css('width') + ' - 4rem)');
  // 心跳频率
  var Hz = 1;

  // 计算ping。为防止乱序，以tickCouonter为数组下标保留20个sendTime
  var tickCounter = -1;
  var sendTime = new Array(20);

  function hbDecoder(data, tickId) {
    // console.log(sendTime);
    var recvTime = (new Date()).getTime();
    var delay = recvTime - sendTime[tickId];
    switch (true) {
      case (delay < 50):
        $('#lb_delay').html("<span class='conn-A'>非常好 " + delay + "</span>");
        break;
      case (delay < 100):
        $('#lb_delay').html("<span class='conn-A'>良好 " + delay + "</span>");
        break;
      case (delay < 500):
        $('#lb_delay').html("<span class='conn-B'>还行 " + delay + "</span>");
        break;
      case (delay < 1000):
        $('#lb_delay').html("<span class='conn-C'>有点慢 " + delay + "</span>");
        break;
      case (delay < 5000):
        $('#lb_delay').html("<span class='conn-D'>很慢 " + delay + "</span>");
        break;
      default:
        $('#lb_delay').html("<span class='conn-E'>你网炸了 " + delay + "</span>");
        break;
    }
    data = JSON.parse(data);

    $('#lb_delay').text();
    $('#lb_status').text(data.data);
  }

  function tick() {
    if (++tickCounter >= sendTime.length) {
      tickCounter = 0;
    }
    // console.log("Tick: "+tickCounter);
    sendTime[tickCounter] = (new Date()).getTime();
    $.post('/quidditch/heartbeat.api')
      .done(function (data) { hbDecoder(data, tickCounter); })
      .fail(function () { $('#lb_delay').html("<span class='conn-E'>你网炸了 " + delay + "</span>"); });
  }

  function gameTicker() {
    setTimeout(gameTicker, 1000/Hz);
    tick();
  }


  gameTicker();

});

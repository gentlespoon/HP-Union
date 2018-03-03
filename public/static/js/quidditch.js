/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   :  [HP-Union]
 * Date   : 2018-03-01
 * Time   : 23:39
 */
 
 
$(document).ready(function (){

  // tick frequency
  var Hz = 2;

  var tickCounter = 0;
  console.log(Number.MAX_SAFE_INTEGER);

  function hbDecoder(data) {
    data = JSON.parse(data);
    $('#status').text(data.data);
  }

  function tick() {
    if (++tickCounter > (Number.MAX_SAFE_INTEGER-1000)) {
      tickCounter = 0;
    }
    console.log("Tick: "+tickCounter);
    $.post('/quidditch/heartbeat.api')
      .done(function (data) { hbDecoder(data); })
      .fail(function () { alert('和服务器的连接中断'); });
    return false;;
  }

  function gameTicker() {
    setTimeout(gameTicker, 1000/Hz);
    tick();
  }


  gameTicker();

});

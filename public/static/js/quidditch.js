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


  function tick() {
    if (++tickCounter > (Number.MAX_SAFE_INTEGER-1000)) {
      tickCounter = 0;
    }
    console.log("Tick: "+tickCounter);
  }

  function gameTicker() {
    setTimeout(gameTicker, 1000/Hz);
    tick();
  }


  gameTicker();

});

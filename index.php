<?php

/*class WebPage {
    function display() {
            echo 'I cannot object to an object';
	        }
}*/
class Item {
    function get() { echo "here is an item"; }
    function post() {}
    function put() {}
    function delete() {}
}

$f3 = require('/var/www/fatfree-master/lib/base.php');
$f3->set('AUTOLOAD','classes/');

$f3->route('GET /',
    function() {
            echo 'Hello, world!';
	        }
);
$f3->route('GET /about', 'WebPage->display');
$f3->route('GET /about/@action', 'WebPage->@action');
$f3->route('GET /brew/@count',
    function($f3) {
            #echo $params['count'].' bottles of beer on the wall.';
            echo $f3->get('PARAMS.count').' bottles of beer on the wall.';
	        }
		);
$f3->route('GET|HEAD /obsoletepage',
    function($f3) {
            $f3->reroute('/about');
	        }
		);
# does not work:
$f3->route('GET /login','Controller\Auth::login');
# http://192.168.10.128/f3/get/cart/123
$f3->map('/cart/@item','Item');  // REST

$f3->run();

?>

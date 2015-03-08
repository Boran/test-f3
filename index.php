<?php

class Item {
    function get() { echo "here is an item"; }
    function post() {}
    function put() {}
    function delete() {}
}

// F3 init
$f3 = require('/var/www/fatfree-master/lib/base.php');
$f3->set('AUTOLOAD','classes/');
$db=new DB\SQL('mysql:host=localhost;port=3306;dbname=boranmpk', 'root', '');
echo Template::instance()->render('tpl/header.htm'); // std page
echo Template::instance()->render('tpl/nav.htm'); 

$f3->set('name','world');

$f3->route('GET /about', 'WebPage->display'); // in classes/WebPage.php
$f3->route('GET /about/@action', 'WebPage->@action');

$job=new DB\SQL\Mapper($db,'jobcard');
$job->load(array('jcard_no=?','3'));
if ($job->dry())
    echo 'No record matching criteria';
$job->cost_note = 'this is expensive!';
$job->save();

$f3->route('GET /',
    function() {
        global $db,$f3;
        #echo 'Hello, world!';
        echo Template::instance()->render('tpl/template.htm');


$f3->set('job', new DB\SQL\Mapper($db,'jobcard'));
$f3->get('job')->virtualfield='jcard_no*2';
# Alternative to subqueries/joins:
$f3->get('job')->status='SELECT Value from jstatus where jobcard.Job_status=jstatus.Code';
$f3->get('job')->load(array('jcard_no=?','3'));
#$f3->get('job')->load(array('jcard_no<?','3'), array('order'=>'lastchange DESC','limit'=>'5'));
#echo "Status=" . $f3->get('job')->status;
echo Template::instance()->render('tpl/job1.htm');
unset($f3->get('job')->status);

$f3->set('result',$db->exec('SELECT * FROM jobcard limit 3'));
echo Template::instance()->render('tpl/job.htm');

    }
);
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

$f3->set('foo','bar');
$foo=$f3->get('foo');

#echo $db->log();
#echo '<br>' 

$logger = new \Log('app-events.log');
$logger->write('called index.php');

// show the page
echo Template::instance()->render('tpl/footer.htm'); // std page
$f3->run();

?>

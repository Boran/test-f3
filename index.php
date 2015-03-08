<?php

// F3 init
$f3 = require_once('../fatfree-master/lib/base.php');
$f3->set('AUTOLOAD', __dir__ . ';classes/');
echo Template::instance()->render('tpl/header.htm'); // std page
echo Template::instance()->render('tpl/nav.htm'); 

// read config settings, with defaults
$f3->config('default.ini');
if (file_exists('config.ini'))
    $f3->config('config.ini');
$debug = $f3->get('debug');

# connect to database
$db=new DB\SQL(sprintf("mysql:host=%s;port=3306;dbname=%s", $f3->get('dbhost'),  $f3->get('dbname')),
  $f3->get('dbuser'), $f3->get('dbpw') );
\Registry::set('db', $db);


$logger = new \Log($f3->get('logfile'));
$logger->write('called index.php');
\Registry::set('logger', $logger);
// </init> done


// example setting variables
$f3->set('name','world');


$job=new DB\SQL\Mapper($db,'jobcard');
$job->load(array('jcard_no=?','3'));
if ($job->dry())
    echo 'No record matching criteria';
$job->cost_note = 'this is expensive!';
$job->save();    // write a value to the DB

// note: most routes are set in the config.ini

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
    unset($f3->get('job')->status);  // clear a virtualfield

    // another query: several entries
    $f3->set('result',$db->exec('SELECT * FROM jobcard limit 3'));
    echo Template::instance()->render('tpl/jobs.htm');
  }
);

$f3->route('GET /brew/@count',
    function($f3) {
            #echo $params['count'].' bottles of beer on the wall.';
            echo $f3->get('PARAMS.count').' bottles of beer on the wall.';
	        }
	);


$f3->set('foo','bar');
$foo=$f3->get('foo');


// show the page
$f3->run();
echo Template::instance()->render('tpl/footer.htm'); // std page


// finish up
if ($debug == 3) {
  #echo $db->log();
  #echo '<br>' 
  $logger->write(\Registry::get('db')->log());
  $execution_time = round(microtime(true) - $f3->get('TIME'), 3);
  $logger->write('Executed in ' . $execution_time . ' secs using ' . round(memory_get_usage() / 1024 / 1024, 2) . '/' . round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB memory/peak');
}

?>

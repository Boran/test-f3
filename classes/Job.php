<?php

class Job extends BaseDB {
  protected $job;
  protected $table='v_jprint';

  public function __construct() {
    parent::__construct();
    $this->db = \Registry::get('db');
    $this->job=new DB\SQL\Mapper($this->db, $this->table);
  }

    function beforeRoute() {
      #echo 'in Job::beforeRoute()>';
    }
    function display() {
      echo 'job::display(): ' . $this->job->Job;
    }

    function get($f3, $args) { 
      //print_r($args);
      $this->job->load(array('Job=?', $args['item']));
      if ($this->job->dry())
        echo 'No record matching criteria';

      #echo 'job::get(): ' . $this->job->Job;
      $f3->set('job', $this->job);
      echo Template::instance()->render('tpl/job.htm');
    }
    function post() {
    }
    function put() {
    }
    function delete() {
    }
}


?>

<?php
require 'autoload.php';
session_start();
require __DIR__.'/helper.php';
danupe()->session()->setCsrf();

include __DIR__.'/routes/web.php';


#!/usr/bin/env php
<?php

use Surgiie\Blade\Blade;

require __DIR__.'/vendor/autoload.php';

$blade = new Blade();

$contents = $blade->render('main', ['title' => 'Hello World']);

dd("CONTENTS", $contents);

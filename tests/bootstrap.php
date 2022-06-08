<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/TestPresenter.php';
require __DIR__ . '/TestCase.php';

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

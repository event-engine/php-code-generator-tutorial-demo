<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

$container = require 'config/container.php';

/** @var \EventEngine\DocumentStore\DocumentStore $documentStore */
$documentStore = $container->get(EventEngine\DocumentStore\DocumentStore::class);

if(!$documentStore->hasCollection('buildings')) {
    echo "Creating collection buildings.\n";
    $documentStore->addCollection('buildings');
}

echo "done.\n";

<?php
// config/spaces.php
require_once __DIR__ . '/../vendor/autoload.php';

use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Aws\S3\S3Client;
use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

function getSpacesFilesystem() {
    $client = new S3Client([
        'version' => 'latest',
        'region' => getenv('DO_SPACES_REGION'),
        'endpoint' => getenv('DO_SPACES_ENDPOINT'),
        'credentials' => [
            'key' => getenv('DO_SPACES_KEY'),
            'secret' => getenv('DO_SPACES_SECRET'),
        ],
    ]);

    $adapter = new AwsS3V3Adapter($client, getenv('DO_SPACES_BUCKET'));
    return new Filesystem($adapter);
}

function getPublicUrl($key) {
    return 'https://' . getenv('DO_SPACES_BUCKET') . '.' . getenv('DO_SPACES_REGION') . '.digitaloceanspaces.com/' . $key;
}
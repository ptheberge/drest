<?php

namespace Drest\Mapping\Driver;

use Doctrine\Common\Annotations;
use Drest\Configuration;
use Drest\DrestException;
use Drest\Mapping\Annotation;
use Drest\Mapping;
use Drest\Mapping\RouteMetaData;

/**
 * The JsonDriver reads a configuration file (config.json) rather than utilizing annotations.
 */
class JsonDriver extends PhpDriver
{
    protected $paths = [];

    public function __construct($paths)
    {
        parent::__construct($paths);

        $filename = self::$configuration_filepath . DIRECTORY_SEPARATOR . self::$configuration_filename;

        if(!file_exists($filename)) { 
            throw new \RuntimeException('The configuration file does not exist at this path: ' . $filename);
        }

        $json = json_decode(file_get_contents($filename), true);

        if($json == null) {
            throw new \RuntimeException('The configuration file does not have valid JSON: ' . $filename);
        }

        $entities = [];
        foreach($json['resources'] as $resource) {
            $entity = $resource['entity'];
            $entities[$entity] = $resource;
            unset($entities[$entity]['entity']);
        }
        $this->classes = $entities;
    }
}

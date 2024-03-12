<?php

namespace App\Models;

use App\Models\Media\YouTube;

class MediaResolver
{
    private $userId;

    private $types = [];

    private $cache = [
        'class' => false,
        'method' => false,
        'args' => false,
        'results' => false,
        'success' => false,
    ];

    public function __construct($userId)
    {
        $this->types = [
            'youtube' => new YouTube($userId),
        ];
    }

    public function collection($userId = false, $returnArray = false)
    {
        $collectionByType = [];
        foreach ($this->types as $class) {
            if (method_exists($class, 'collection')) {
                $collectionByType[$class->typeName] = $class->collection($userId);
            }
        }

        if (! $returnArray) {
            return response()->json([
                'collection' => $collectionByType,
                'code' => 200,
                'message' => 'Success',
            ], 200);
        }

        return $collectionByType;
    }

    public function dispatch($type, $action, $input)
    {
        $class = $this->types[$type];
        $methods = get_class_methods($class);
        $methods = array_diff($methods, ['__construct']);

        //@TODO make sure my meta logic with arguments vs params is correct
        if (in_array($action, $methods)) {
            $arguments = $this->matchArguments($class, $action, $input);
            $results = $class->{$action}(...$arguments);
            //update cache
            $this->cache($class, $action, $arguments, $results);
        } else {
            //clear cache
            $this->cache();
        }

        return $this;
    }

    public function didError()
    {
        return $this->cache['success'] ? false : true;
    }

    public function getJSONResponse()
    {
        if ($this->didError()) {
            return response()->json([
                'code' => 401,
                'message' => 'Failed to resolve action',
            ], 401);
        }

        return response()->json($this->cache['results'], 200);
    }

    private function cache($class = false, $method = false, $args = false, $results = false)
    {
        $this->cache = [
            'class' => $class,
            'method' => $method,
            'args' => $args,
            'results' => $results,
            'success' => ($results ? true : false),
        ];
    }

    private function matchArguments($class, $method, $arguments)
    {
        $params = [];

        $ref = new \ReflectionMethod($class, $method);
        foreach ($ref->getParameters() as $param) {
            $params[] = $param->name;
        }

        $data = [];
        //match param to available arguments
        foreach ($params as $param) {
            //fill array will correct param order instead of key => value
            $data[] = $arguments[$param];
        }

        return $data;
    }
}

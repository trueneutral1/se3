<?php

namespace se3;

/**
 * Class DataLoaderConfig
 * @package se3
 */
class DataLoaderConfig
{
    private $host, $port, $user, $password;

    /**
     * DataLoaderConfig constructor.
     * @param $host
     * @param $post
     * @param $user
     * @param $password
     */
    public function __construct($host, $port, $user, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

}
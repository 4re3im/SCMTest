<?php

/**
 * Interface RestAPI
 * Simple Interface to follow
 */
interface RestAPI
{
    public function create($id = null);

    public function read($id = null);

    public function update($id = null);

    public function delete($id = null);

}
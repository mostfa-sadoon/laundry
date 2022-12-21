<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function signin($request);
    public function createUser($request);
    public function verifyphone($request);
    public function addaddress($request,$userid);
    public function deleteadress($address_id);
}

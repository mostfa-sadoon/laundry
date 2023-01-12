<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function signin($request);
    public function createUser($request);
    public function verifyphone($request);
    public function addaddress($request,$userid);
    public function deleteadress($address_id);
    public function userinfo($id);
    public function updateuser($request,$id);
    public function updatephone($id);
    public function getaddresses($user);
    public function updateaddress($request,$user_id);
    public function uploadImage($photo_name,$folder);
}

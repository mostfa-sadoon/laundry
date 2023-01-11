<?php
function uploadImage($photo_name, $folder)
{
	$image = $photo_name;
	$image_name = time() . '' . $image->getClientOriginalName();
	$destinationPath = public_path($folder);
	$image->move($destinationPath, $image_name);
	return $image_name;
}

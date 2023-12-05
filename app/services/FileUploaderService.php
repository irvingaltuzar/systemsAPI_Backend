<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FileUploaderService
{
	public function store($file, Array $payload)
	{
		$now = Carbon::now()->format('Y-m-d-H-m-s');

		$alias_file=substr(str_shuffle(str_repeat('ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz01234567890123456789012345678901234567890123456789',15)),0,15);

		$ext = $file->getClientOriginalExtension();
		$path = "{$payload['type']}/{$payload['id']}/";
		$filename = "{$alias_file}.{$ext}";

		$file->storeAs($path, "{$filename}", ['disk' => 'Publico']);
		// Storage::disk('public')->put($path, $file);
		return [
			'filename' => $filename
		];
	}

	public function uploadFile()
	{
		# code...
	}

	public function upload($file, Array $payload)
	{
		$alias_file = substr(str_shuffle(str_repeat('ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz01234567890123456789012345678901234567890123456789',15)),0,15);

		$ext = $file['ext'];


		$filename = "{$alias_file}.{$ext}";

		$path = "{$payload['type']}/{$payload['id']}/{$filename}";

		$b64Image  = preg_replace('#^data:image/\w+;base64,#i', '', $file['data']);
		$imageFile = base64_decode($b64Image);

		Storage::disk('Publico')->put($path, $imageFile);

		return [
			'filename' => $filename
		];
	}
}

<?php

namespace App\Utilities;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class Utility
{
    public static function message(
        Bool $status,
        array|Collection $errors = [],
        array|Collection|AnonymousResourceCollection|JsonResource $data = []
    ): array {
        $started = [
            "meta" => [
                "success" => $status,
                "errors" => $errors
            ]
        ];
        if (!empty($data)) {
            $started["data"] = $data;
        }
        return $started;
    }
}

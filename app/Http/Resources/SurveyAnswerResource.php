<?php

namespace App\Http\Resources;

use App\Http\Resources\SurveyResource;
use App\Models\Survey;
use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'survey' => new SurveyResource($this->survey),
            'end_data' => (new DateTime($this->end_data))->format('Y-m-d H:i:s'),
        ];
    }
}

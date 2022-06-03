<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Survey;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $table = 'survey_questions';

    protected $fillable = ['type', 'question', 'data', 'description', 'survey_id'];

    public function survey(){
        return $this->belongsTo(Survey::class);
    }
}

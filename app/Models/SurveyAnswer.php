<?php

namespace App\Models;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    use HasFactory;

    const CREATED_AT = null;

    const UPDATED_AT = null;

    protected $fillable = ['survey_id', 'start_date' ,'end_data'];

    public function survey(){
        return $this->belongsTo(Survey::class);
    }
}

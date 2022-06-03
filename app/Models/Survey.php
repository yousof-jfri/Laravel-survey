<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SurveyQuestion;
use App\Models\SurveyAnswer;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'slug', 'status', 'expire_data', 'image'];

    public function questions(){
        return $this->hasMany(SurveyQuestion::class);
    }

    public function answers(){
        return $this->hasMany(SurveyAnswer::class);
    }
    
}

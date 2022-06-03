<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeAnswersRequest;
use App\Models\Survey;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Resources\SurveyResource;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionAnswer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return SurveyResource::collection(Survey::where('user_id', $user->id)->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();

        

        if($data['image']){
            $relativePath = $this->saveImage($request->image);
            $image = $relativePath;
            $data['image'] = $image;
        }

        $data['slug'] = Str::slug($request->title);

        $survey = Survey::create($data);

        foreach($data['questions'] as $question){
            $question['survey_id'] = $survey->id;
            $this->createQuestion($question);
        }



        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show(Survey $survey, Request $request)
    {
        $user = $request->user();

        if($user->id !== $survey->user_id){
            return abort(403, 'Unauthorizes action');
        }

        return new SurveyResource($survey);
    }

    public function showForGuest(Survey $survey){
        return new SurveyResource($survey);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSurveyRequest  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        $data = $request->validated();

        if(isset($data['image'])){
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;

            if($survey->image){
                $absolutePath = public_path($survey->image);
                File::delete($absolutePath);
            }
        }

        $data['slug'] = Str::slug($data['title']);

        $survey->update($data);

        // Get ids as plain array of existing question
        $existingIds = $survey->questions()->pluck('id')->toArray();

        // Get ids as plain array of new question
        $newIds = Arr::pluck($data['questions'], 'id');

        // Find Questions to delete
        $toDelete = array_diff($existingIds, $newIds);

        // Find questions to add
        $toAdd = array_diff($newIds, $existingIds);

        // Delete questions by $toDelete array 
        SurveyQuestion::destroy($toDelete);

        // create new Question
        foreach($data['questions'] as $question){
            if(in_array($question['id'], $toAdd)){
                $question['survey_id'] = $survey->id;
                $this->createQuestion($question);
            }
        }


        // update existing question
        $questionMap = collect($data['questions'])->keyBy('id');

        foreach($survey->questions as $question){
            if(isset($questionMap[$question->id])){
                $this->updateQuestion($question, $questionMap[$question->id]);
            }
        }

        return new SurveyResource($survey);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survey $survey, Request $request)
    {
        $user = $request->user();

        if($user->id !== $survey->user_id){
            return abort(403, 'Unauthorized action');
        }

        if($survey->image){
            if(File::exists(public_path($survey->image))){
                File::delete(public_path($survey->image));
            }
        }

        $survey->delete();
        return response('survey deleted succesfully', 204);
    }

    private function saveImage($image)
    {
        if(preg_match('/^data:image\/(\w+);base64,/', $image, $type)){
            $image = substr($image, strpos($image, ',') + 1);

            $type = strtolower($type[1]);

            if(!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])){
                throw new \Exception('invalid image type');
            }

            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if($image == false){
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception("didn' match data URL with image data");
        }

        $dir = 'images/';
        $file = Str::random() . '.' .$type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;

        if(!File::exists($absolutePath)){
            File::makeDirectory($absolutePath, 0755, true);
        }

        file_put_contents($relativePath, $image);

        return $relativePath;
    }

    private function createQuestion($data){
        if(is_array($data['data'])){
            $data['data'] = json_encode($data['data']);
        }

        $validator = Validator::make($data, [
            'question' => 'required|string',
            'type' => ['required'],
            'description' => ['nullable'],
            'data' => 'present',
            'survey_id' => 'exists:App\Models\Survey,id'
        ]);

        return SurveyQuestion::create($validator->validated());
    }

    private function updateQuestion(SurveyQuestion $question, $data){
        if(is_array($data['data'])){
            $data['data'] = json_encode($data['data']);
        }

        $validator = Validator::make($data, [
            'id' => 'exists:App\Models\SurveyQuestion,id',
            'questions' => 'required|string',
            'type' => 'required',
            'description' => 'nullable|string',
            'data' => 'present'
        ]);

        return $question->update($validator->validated());
    }

    public function saveAnswers(storeAnswersRequest $request, Survey $survey){
        $data = $request->validated();

        $surveyAnswer = SurveyAnswer::create([
            'survey_id' => $survey->id,
            'start_date' => date('Y-m-d H:i:s'),
            'end_data' => date('Y-m-d H:i:s'),
        ]);

        foreach($data['answers'] as $questionId => $answer){
            $question = SurveyQuestion::where(['id' => $questionId, 'survey_id' => $survey->id])->get();

            if(!$question){
                return response("invalide question id: \"$questionId\"", 490);
            }

            $answers = [
                'survey_question_id' => $questionId,
                'survey_answer_id' => $surveyAnswer->id,
                'answer' => is_array($answer) ? json_encode($answer) : $answer,
            ];

            SurveyQuestionAnswer::create($answers);
        }

        return response('success', 201);
    }
}

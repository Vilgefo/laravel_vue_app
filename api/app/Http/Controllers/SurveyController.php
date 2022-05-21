<?php

namespace App\Http\Controllers;

use App\Classes\Survey\AnswerManager;
use App\Classes\Survey\SurveyManager;
use App\Http\Requests\StoreSurveyAnswerRequest;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Models\Survey;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use App\Http\Resources\SurveyResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Mockery\Exception;


class SurveyController extends Controller
{
    private SurveyManager $surveyManager;

    public function __construct(SurveyManager $surveyManager)
    {
        $this->surveyManager = new $surveyManager();
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $surveys = Survey::where('user_id', $user->id)->paginate(10);
        return SurveyResource::collection($surveys);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSurveyRequest $request
     * @return SurveyResource
     */
    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();

        $survey = $this->surveyManager->addNewSurvey($data);

        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     *
     * @param Survey $survey
     * @return SurveyResource
     */
    public function show(Request $request, Survey $survey)
    {
        $user = $request->user();
        if ($user->id !== $survey->user_id) {
            return abort(403, 'Unauthorized action');
        }
        return new SurveyResource($survey);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSurveyRequest $request
     * @param Survey $survey
     * @return SurveyResource
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        $data = $request->validated();

        $updatedSurvey = $this->surveyManager->update($data, $survey);

        return new SurveyResource($updatedSurvey);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Survey $survey
     * @return Response
     */
    public function destroy(Survey $survey, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $survey->user_id) {
            return abort(403, 'Unauthorized action');
        }
        $this->surveyManager->deleteSurvey($survey);

        return response('', 204);
    }

    /**
     * @param StoreSurveyAnswerRequest $request
     * @param Survey $survey
     * @return Application|ResponseFactory|Response
     */
    public function storeAnswer(StoreSurveyAnswerRequest $request, Survey $survey)
    {
        $validated = $request->validated();

        $answerManager = new AnswerManager();
        try {
            $answerManager->addAnswer($survey, $validated['answers']);
        } catch (Exception $e) {
            return response($e->getMessage(), $e->getCode());
        }

        return response("", 201);

    }

    /**
     * @param Survey $survey
     * @return SurveyResource
     * @throws \Exception
     */
    public function showForGuest(Survey $survey)
    {
        return new SurveyResource($survey);
    }

}

<?php
namespace App\Classes\Survey;

use App\Classes\FileComponent;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class SurveyManager
{
    /**
     * @param array $surveyData
     * @return Survey
     * @throws ValidationException
     */
    public function addNewSurvey(array $surveyData): Survey
    {
        if (isset($surveyData['image'])) {
            $relativePath = FileComponent::saveImage($surveyData['image']);
            $surveyData['image'] = $relativePath;
        }
        $survey = Survey::create($surveyData);
        $questionManager = new QuestionManager();
        foreach ($surveyData['questions'] as $question) {
            $question['survey_id'] = $survey->id;
            $questionManager->createQuestion($question);
        }
        return $survey;
    }

    /**
     * @param array $data
     * @param Survey $survey
     * @return Survey
     * @throws ValidationException
     */
    public function update(array $data, Survey $survey): Survey
    {
        // Check if image was given and save on local file system
        if (isset($data['image'])) {
            $relativePath = FileComponent::saveImage($data['image']);
            $data['image'] = $relativePath;

            // If there is an old image, delete it
            if ($survey->image) {
                $absolutePath = public_path($survey->image);
                File::delete($absolutePath);
            }
        }

        // Update survey in the database
        $survey->update($data);

        // Get ids as plain array of existing questions
        $existingIds = $survey->questions()->pluck('id')->toArray();
        // Get ids as plain array of new questions
        $newIds = Arr::pluck($data['questions'], 'id');
        // Find questions to delete
        $toDelete = array_diff($existingIds, $newIds);
        //Find questions to add
        $toAdd = array_diff($newIds, $existingIds);

        // Delete questions by $toDelete array
        SurveyQuestion::destroy($toDelete);

        // Create new questions
        $questionManager = new QuestionManager();
        foreach ($data['questions'] as $question) {
            if (in_array($question['id'], $toAdd)) {
                $question['survey_id'] = $survey->id;
                $questionManager->createQuestion($question);
            }
        }

        // Update existing questions
        $questionMap = collect($data['questions'])->keyBy('id');
        foreach ($survey->questions as $question) {
            if (isset($questionMap[$question->id])) {
                $questionManager->updateQuestion($question, $questionMap[$question->id]);
            }
        }

        return $survey;
    }

    /**
     * @param Survey $survey
     * @return bool
     */
    public function deleteSurvey(Survey $survey): bool
    {
        $survey->delete();
        // If there is an old image, delete it
        if ($survey->image) {
            $absolutePath = public_path($survey->image);
            File::delete($absolutePath);
        }

        return true;
    }
}

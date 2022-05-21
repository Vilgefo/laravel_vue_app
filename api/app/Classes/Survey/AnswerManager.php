<?php

namespace App\Classes\Survey;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionAnswer;
use Mockery\Exception;

class AnswerManager
{
    /**
     * @param Survey $survey
     * @param array $answers
     * @return SurveyAnswer
     */
    public function addAnswer(Survey $survey, array $answers): SurveyAnswer
    {
        $surveyAnswer = SurveyAnswer::create([
            'survey_id' => $survey->id,
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s'),
        ]);

        foreach ($answers as $questionId => $answer) {
            $question = SurveyQuestion::where(['id' => $questionId, 'survey_id' => $survey->id])->get();
            if (!$question) {
                Throw new Exception("Invalid question ID: \"$questionId\"", 400);
            }
            clock($questionId);
            $data = [
                'survey_question_id' => $questionId,
                'survey_answer_id' => $surveyAnswer->id,
                'answer' => is_array($answer) ? json_encode($answer) : $answer
            ];

            SurveyQuestionAnswer::create($data);
        }

        return $surveyAnswer;
    }
}

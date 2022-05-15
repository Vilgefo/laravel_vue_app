<?php

namespace App\Classes\Survey;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuestionManager
{
    /**
     * @param SurveyQuestion $question
     * @param array $data
     * @return bool
     * @throws ValidationException
     */
    public function updateQuestion(SurveyQuestion $question, array $data): bool
    {
        if (is_array($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }
        $validatedData = $this->validateQuestionData($data);

        return $question->update($validatedData);
    }

    /**
     * Create a question and return
     *
     * @param $data
     * @return SurveyQuestion
     * @throws ValidationException
     */
    public function createQuestion(array $data): SurveyQuestion
    {
        if (is_array($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }
        $validatedData = $this->validateQuestionData($data);

        return SurveyQuestion::create($validatedData);
    }

    /**
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    private function validateQuestionData(array $data): array
    {
        $validator = Validator::make($data, [
            'question' => 'required|string',
            'type' => ['required', Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX,
            ])],
            'description' => 'nullable|string',
            'data' => 'present',
            'survey_id' => 'exists:App\Models\Survey,id'
        ]);
        return $validator->validated();
    }
}

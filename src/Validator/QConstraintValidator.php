<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class QConstraintValidator extends ConstraintValidator {

    public function validate($questionAdd, Constraint $constraint)
    {
        foreach ($questionAdd->getExam()->getQuestions() as $question) {
            if ($question != $questionAdd && $questionAdd->getTitle() == $question->getTitle()) {
                $this->context->buildViolation($constraint->uniqueTitleError)
                    ->atPath('title')
                    ->addViolation();

                break;
            }
        }

        $count = $questionAdd->getPropositions()->count();
        foreach ($questionAdd->getPropositions() as $proposition) {
            if ($proposition->getProposition() == "") {
                $count--;
            }
        }

        if ($count == 0) {
            $this->context->buildViolation($constraint->propositionArrayEmpty)
                ->atPath('task')
                ->addViolation();
        }
    }
}
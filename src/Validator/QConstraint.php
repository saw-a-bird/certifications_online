<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class QConstraint extends Constraint
{
    public $uniqueTitleError = 'This question title is already used.';
    public $propositionArrayEmpty = 'Please add some propositions before validating.';
    public $mode = 'strict'; // If the constraint has configuration options, define them as public properties

    public function getTargets(){
        return self::CLASS_CONSTRAINT;
    }

}
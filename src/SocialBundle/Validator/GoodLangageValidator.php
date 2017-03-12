<?php

namespace SocialBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GoodLangageValidator extends ConstraintValidator
{
  public function validate($value, Constraint $constraint)
  {
      $langages = ["HTML", "CSS", "PHP", "JS", "Python", "Jquery", "Bootstrap", "GIT", "Autre", "Illustrator", "Photoshop", "Wordpress"];
      
      if (!in_array($value, $langages))
      {
          $this->context->addViolation($constraint->message);
      }
  }
}

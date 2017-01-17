<?php

namespace SocialBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class GoodLangage extends Constraint
{
  public $message = "Sélectionnez un langage parmis la liste proposée";
}

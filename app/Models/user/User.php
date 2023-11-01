<?php

declare(strict_types = 1);

namespace App\Models;

use Nextras\Orm\Entity\Entity;

/**
 * @property-read int                   $id         {primary}
 * @property      string                $password
 * @property      string                $email
 * @property      string                $name
 * @property      string                $surname
 *
 * @repository App\Models\UserRepository
 * @table users
 */

 class User extends Entity
{

}
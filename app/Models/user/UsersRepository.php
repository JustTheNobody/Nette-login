<?php

namespace App\Models;

use Nextras\Orm\Repository\Repository;


/**
 * @method Tag|NULL getById( $id )
 * @method Tag|NULL getByName( $username )
 */

class UsersRepository extends Repository
{
	static function getEntityClassNames(): array
	{
		return [User::class];
	}

}

<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\User;

class Moderator extends Authenticatable
{
    protected $table = 'moderators';

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'sub_plebbit_id'
    ];

    public function getBySubPlebbitId($id)
    {
        return $this->select('user_id', 'sub_plebbit_id', 'username')
            ->join('users', 'moderators.user_id', '=', 'users.id')
            ->where('sub_plebbit_id', $id)->get();
    }

    public function validateMods($mods_string) {
        $mods = explode(',', $mods_string);

        $invalid = '';
        foreach ($mods as $mod) {
            $u = User::where('username', $mod)->first();
            if (!$u) {
                $invalid.= $mod . ',';
            }
        }

        if ($invalid == '') {
            return true;
        }
        return false;
    }

    public function isMod($user_id, $sub_plebbit)
    {
        if (env('ADMIN_ID') == $user_id) {
            return true;
        }
        if ($user_id == $sub_plebbit->owner_id) {
            return true;
        }
        $check = $this->where('user_id', $user_id)->where('sub_plebbit_id', $sub_plebbit->id)->first();
        if ($check) {
            return true;
        } else {
            return false;
        }
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model{
	protected $table = 'roles';
	protected $fillable = ['role_name','main_menu_ids','sub_menu_ids','crud_rights','hide_confidentiality','username','status','created_at','updated_at'];
	protected $primaryKey = 'id';
	public $timestamps = true;
}

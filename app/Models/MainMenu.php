<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainMenu extends Model
{
    protected $fillable = ['main_menu_id', 'title', 'title_id', 'menu_type', 'status','date'];
}

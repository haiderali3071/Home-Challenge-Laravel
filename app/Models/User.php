<?php
  
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
  
class User extends Authenticatable
{
    use HasFactory, HasApiTokens;
   
    protected $fillable = [
        'username', 
        'password',
    ]; 
}
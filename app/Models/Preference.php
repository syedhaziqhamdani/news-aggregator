<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="Preference",
 *     type="object",
 *     description="User preference schema",
 *     @OA\Property(property="id", type="integer", description="Preference ID"),
 *     @OA\Property(property="user_id", type="integer", description="User ID associated with the preferences"),
 *     @OA\Property(
 *         property="sources",
 *         type="array",
 *         description="List of preferred news sources",
 *         @OA\Items(type="string")
 *     ),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         description="List of preferred categories",
 *         @OA\Items(type="string")
 *     ),
 *     @OA\Property(
 *         property="authors",
 *         type="array",
 *         description="List of preferred authors",
 *         @OA\Items(type="string")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
 * )
 */
class Preference extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sources',
        'categories',
        'authors',
    ];

    protected $casts = [
        'sources' => 'array',
        'categories' => 'array',
        'authors' => 'array',
    ];
}

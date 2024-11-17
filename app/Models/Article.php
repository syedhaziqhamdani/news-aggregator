<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     description="Article schema",
 *     @OA\Property(property="id", type="integer", description="Article ID"),
 *     @OA\Property(property="title", type="string", description="Title of the article"),
 *     @OA\Property(property="description", type="string", description="Description of the article"),
 *     @OA\Property(property="source", type="string", description="Source of the article"),
 *     @OA\Property(property="category", type="string", description="Category of the article"),
 *     @OA\Property(property="author", type="string", description="Author of the article"),
 *     @OA\Property(property="published_at", type="string", format="date-time", description="Publication date of the article"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
 * )
 */
class Article extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'url',
        'source',
        'category',
        'published_at',
    ];
}

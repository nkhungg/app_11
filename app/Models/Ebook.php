<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ebook extends Model {
    protected $fillable = [ 'product_id', 'file_path', 'preview_path' ];

    public function product() {
        return $this->belongsTo( Product::class );
    }
}

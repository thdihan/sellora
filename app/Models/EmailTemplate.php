<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'slug',
        'subject_tpl',
        'body_tpl',
        'description',
        'enabled'
    ];
    
    protected $casts = [
        'enabled' => 'boolean',
    ];
    
    /**
     * Scope for enabled templates
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }
    
    /**
     * Find template by slug
     */
    public static function findBySlug(string $slug)
    {
        return static::where('slug', $slug)->where('enabled', true)->first();
    }
    
    /**
     * Render template with data
     */
    public function render(array $data = [])
    {
        $subject = $this->subject_tpl;
        $body = $this->body_tpl;
        
        foreach ($data as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $subject = str_replace($placeholder, $value, $subject);
            $body = str_replace($placeholder, $value, $body);
        }
        
        return [
            'subject' => $subject,
            'body' => $body
        ];
    }
}

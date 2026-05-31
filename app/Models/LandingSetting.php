<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_badge',
        'primary_cta_text',
        'primary_cta_url',
        'secondary_cta_text',
        'secondary_cta_url',
        'support_title',
        'support_text',
        'support_whatsapp',
        'featured_section_title',
        'featured_section_subtitle',
        'footer_short_text',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'og_image_url',
    ];
}

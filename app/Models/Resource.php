<?php

namespace App\Models;

use Validator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Resource extends Model
{
// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // TRAITS
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // VARIABLES
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    protected $fillable = [
        'judul',
        'konten',
        'direktori',
        'subdirektori',
        'thumbnail',
        'media_tipe',
        'media_url',
    ];

    protected $hidden = [
    ];

    protected $casts = [
    ];

    protected $dates = [
        'published_at',
        'deleted_at'
    ];

    protected $observables = [
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    protected $appends = ['status'];

    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // CONFIGURATIONS
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // BOOT
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // CONSTRUCT
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // RELATIONSHIP
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // STATIC FUNCTION
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // FUNCTION
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function getRules()
    {
        $rules                       = [];
        $rules['user_id']            = ['required', 'string', Rule::exists(User::class, 'id')];
        $rules['judul']              = ['required', 'string', 'max:255'];
        $rules['direktori']          = ['required', 'string', 'max:255'];
        $rules['konten']             = ['required', 'string'];
        $rules['thumbnail']          = ['required', 'string'];
        $rules['media_tipe']         = ['required', 'in:other,image,audio,video'];
        $rules['media_url']          = ['required', 'string'];
        $rules['published_at']       = ['nullable', 'date'];

        return $rules;
    }

    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // ACCESSOR
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function getStatusAttribute(){
        if(is_null($this->published_at)) {
            return 'DRAFT';
        }

        return 'PUBLISHED';
    }
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // MUTATOR
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // QUERY
    // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function scopeFilter($q, Array $filters, Bool $isCount = false)
    {
        /*----------  Validate  ----------*/
        $rules = [
            'published_at_gte' => ['nullable', 'date'],
            'published_at_lte' => ['nullable', 'date'],
            'published' 	   => ['nullable', 'boolean'],
            'skip'             => ['numeric', 'integer', 'gte:0'],
            'take'             => ['numeric', 'integer', 'gte:1'],
        ];
        Validator::make($filters, [$rules])->validate();

        /*----------  Query  ----------*/
        foreach ($filters as $field => $val)
        {
            switch (strtolower($field))
            {
                case 'except_ids'       : $q = $q->whereNotIn('id', is_array($val) ? $val                                             : [$val]); break;
                case 'search'           : $q = $q->search('*'.$val.'*'); break;
                case 'published_at_gte' : $q = $q->where('published_at', '>=', $val); break;
                case 'published_at_lte' : $q = $q->where('published_at', '<=', $val); break;
                case 'published' 		: $q = $q->published($val); break;
                case 'direktori'        : $q = $q->where('direktori', $val); break;
                case 'subdirektori'     : $q = $q->where('subdirektori', $val); break;
                case 'user_id'          : $q = $q->where('user_id', $val); break;

                case 'orderby': $q                 = $q->orderby($this->getTable() . '.' . (in_array($val, ['published_at', 'judul', 'created_at', 'updated_at']) ? $val : 'published_at'), $filters['orderdesc'] ? 'desc' : 'asc');break;
            }
        }

        return $q;
    }

    public function scopeSearch($q, String $v)
    {
        return $q->where($this->getTable() . '.judul', 'like', str_replace('*', '%', $v));
    }

    public function scopePublished($q, Bool $v = true)
    {
        if ($v)
        {
            return $q->where(function($q){ $q->whereNotNull('published_at')->where('published_at', "<", now());} );
        }
        else
        {
            return $q->where(function($q){ $q->whereNull('published_at')->orwhere('published_at', ">=", now());} );
        }
    }

    public function scopeUserId($q, String $v)
    {
        return $q->where('user_id', '=', $v);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use FFMpeg;
use Auth;

class Media extends Model
{
    // use SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function strain() {
        return $this->belongsTo(Strain::class);
    }

    public function taggedUsers() {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    public function taggedPortal() {
        return $this->belongsTo('App\User', 'tagged_portal');
    }

    public function taggedStrain()
    {
        return $this->belongsTo(Strain::class, 'tagged_strain');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'target_id')->where('model', 'post');
    }

    public function comments() {
        return $this->hasMany(Comment::class, 'target_id')->where('target_model', 'media');
    }

    public function menu() {
        return $this->hasOne(Menu::class);
    }

    public function convert_video() {
        $old_filepath = $this->url;
        $path_parts = pathinfo($old_filepath);
        $filename = $path_parts['filename'];
        $extension = $path_parts['extension'];
        $new_filepath = $path_parts['dirname']."/".$filename . "_1.".$extension;

        $lowBitrateFormat = (new \FFMpeg\Format\Video\X264('libmp3lame', 'libx264'))->setKiloBitrate(250);
        FFMpeg::fromDisk('video_root')
            ->open($old_filepath)
            ->addFilter(function ($filters) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension(320, 240));
            })
            ->export()
            ->toDisk('video_root')
            ->inFormat($lowBitrateFormat)
            ->save($new_filepath);
        $this->update(['url' => $new_filepath]);
        unlink(public_path($old_filepath));
        $this->createThumbnail();
        return true;
    }

    public function createThumbnail() {
        $filePath = $this->url;
        $path_parts = pathinfo($filePath);
        $filename = $path_parts['filename'];
        $new_filepath = "uploaded/image/".$filename . ".jpg";

        FFMpeg::fromDisk('video_root')
            ->open($filePath)
            ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(0.2))
            ->save($new_filepath);
        return true;
    }

    public function flags(){
        return $this->morphMany('App\Models\Flag', 'flaggable');
    }

    public function is_flag() {
        return $this->flags()->where('user_id', Auth::id())->exists();
    }
}

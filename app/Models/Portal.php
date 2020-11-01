<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Companychat;
use Carbon\Carbon;

class Portal extends Model
{
    protected $guarded = [];

    protected $with = ['media'];

    public function media() {
        return $this->belongsTo('App\Media', 'media_id', 'id');
    }

    public function logo() {
        return $this->belongsTo(Media::class, 'media_id', 'id')->where('model', 'logo');
    }

    public function allMedia() {
        return $this->hasMany(Media::class);
    }

    public function allTagged() {
        return $this->hasMany(Media::class, 'tagged_portal');
    }

    public function comments() {
        return $this->hasMany(Comment::class, 'target_id')->where('target_model', 'portal')->where('parent_id', 0);
    }

    public function taggedMedia() {
        return $this->belongsTo(Media::class, 'tagged_portal');
    }

    public function coupon() {
        return $this->hasOne(Coupon::class);
    }

    public function menus() {
        return $this->hasMany(Menu::class);
    }

    public function get_distance($latitude, $longitude) {
        $portalLat = $this->latitude;
        $portalLong = $this->longitude;

        $latFrom = deg2rad($latitude);
        $lonFrom = deg2rad($longitude);
        $latTo = deg2rad($portalLat);
        $lonTo = deg2rad($portalLong);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        $portdistance = $angle * 3959;

        return round($portdistance, 1);
    }

    public function get_shop_status() {
        $shop_status = 2;
        $timezone = $this->timezone;
        $portal_now = Carbon::now($timezone);
        $carbonTime = $portal_now;
        $currentDay = $portal_now->englishDayOfWeek;
        switch ($currentDay) {
            case 'Monday':
                if (1 === $this->mon_closed) {
                    $shop_status = '1';
                } elseif (2 === $this->mon_closed) {
                    $shop_status = '2';
                } else {
                    $open = $this->mon_open;
                    $close = $this->mon_close;

                    $shop_status = self::isOpen($open, $close, $timezone);
                }

                break;
            case 'Tuesday':
                if (1 === $this->tue_closed) {
                    $shop_status = '1';
                } elseif (2 === $this->tue_closed) {
                    $shop_status = '2';
                } else {
                    $open = $this->tue_open;
                    $close = $this->tue_close;

                    $shop_status = self::isOpen($open, $close, $timezone);
                }

                break;
            case 'Wednesday':
                if (1 === $this->wed_closed) {
                    $shop_status = '1';
                } elseif (2 === $this->wed_closed) {
                    $shop_status = '2';
                } else {
                    $open = $this->wed_open;
                    $close = $this->wed_close;

                    $shop_status = self::isOpen($open, $close, $timezone);
                }

                break;
            case 'Thursday':
                if (1 === $this->thu_closed) {
                    $shop_status = '1';
                } elseif (2 === $this->thu_closed) {
                    $shop_status = '2';
                } else {
                    $open = $this->thu_open;
                    $close = $this->thu_close;

                    $shop_status = self::isOpen($open, $close, $timezone);
                }

                break;
            case 'Friday':
                if (1 === $this->fri_closed) {
                    $shop_status = '1';
                } elseif (2 === $this->fri_closed) {
                    $shop_status = '2';
                } else {
                    $open = $this->fri_open;
                    $close = $this->fri_close;

                    $shop_status = self::isOpen($open, $close, $timezone);
                }

                break;
            case 'Saturday':
                if (1 === $this->sat_closed) {
                    $shop_status = '1';
                } elseif (2 === $this->sat_closed) {
                    $shop_status = '2';
                } else {
                    $open = $this->sat_open;
                    $close = $this->sat_close;

                    $shop_status = self::isOpen($open, $close, $timezone);
                }

                break;
            case 'Sunday':
                if (1 === $this->sun_closed) {
                    $shop_status = 1;
                } elseif (2 === $this->sun_closed) {
                    $shop_status = 2;
                } else {
                    $open = $this->sun_open;
                    $close = $this->sun_close;

                    $shop_status = self::isOpen($open, $close, $timezone);
                }

                break;
            default:
                break;
        }
        return $shop_status;
    }

    public function isOpen($open, $close, $timezone) {
        $fopen = Carbon::create($open, $timezone);
        $fclose = Carbon::create($close, $timezone);
        $currentTime = Carbon::now($timezone);
        if ($currentTime->between($fopen, $fclose)) {
            return 2;
        }
        return 1;
    }
}

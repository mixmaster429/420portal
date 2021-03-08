<?php

namespace App;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Models\UserChat;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject //, MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification() {
        $this->notify(new VerifyEmail);
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    /////////////////////////////////////////////////

    protected $with = ['profilePic'];

    protected $appends = ["unread_message_user_count"];

    public function getUnreadMessageUserCountAttribute()
    {
        $user_ids = [];
        $messages = UserChat::where('user_to', $this->id)->where('read', 0)->get();

        foreach($messages as $message)
        {
            array_push($user_ids, $message->user_id);
        }

        return sizeOf(array_unique($user_ids));
    }

    public function medias() {
        return $this->hasMany('App\Models\Media');
    }

    public function profilePic() {
        return $this->belongsTo('App\Models\Media', 'media_id');
    }

    public function userchat() {
        return $this->hasMany('App\Models\UserChat');
    }

    public function taggedMedia() {
        return $this->belongsToMany('App\Models\Media')->withTimestamps();
    }

    public function notifications() {
        return $this->hasMany('App\Models\Notification');
    }

    public function notification_filter() {
        return $this->hasOne('App\Models\NotificationFilter');
    }

    public function check_notification_filter($value) {
        if($this->notification_filter && $this->notification_filter->value){
            $value_array = explode(', ', $this->notification_filter->value);
            return in_array($value, $value_array);
        } else {
            return false;
        }
    }    

    // From Portal

    
    public function comments() {
        return $this->hasMany(Comment::class, 'target_id')->where('target_model', 'portal')->where('parent_id', 0);
    }

    public function taggedPortalMedia() {
        return $this->hasMany('App\Models\Media', 'tagged_portal');
    }

    public function coupon() {
        return $this->hasOne('App\Models\Coupon', 'portal_id');
    }

    public function menus() {
        return $this->hasMany('App\Models\Menu', 'portal_id');
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
        $fopen = Carbon::createFromFormat('h:i A', $open, $timezone);
        $fclose = Carbon::createFromFormat('h:i A', $close, $timezone);
        if($fopen->isAfter($fclose)) {
            $fclose->addDays(1);
        }
        $currentTime = Carbon::now($timezone);
        if ($currentTime->between($fopen, $fclose)) {
            return 2;
        }
        return 1;
    }




}

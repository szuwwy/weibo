<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    //boot 方法会在用户模型类完成初始化之后进行加载，因此我们对事件的监听需要放在该方法中.static 方法体调用static::create()就会报错
    public static function boot()
    {
        parent::boot();
        //creating 用于监听模型被创建之前的事件，created 用于监听模型被创建之后的事件
        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }

    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    //发布的微博
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function feed()
    {
        $user_ids = Auth::user()->followings->pluck('id')->push(Auth::user()->id)->toArray();
        return Status::whereIn('user_id', $user_ids)->with('user')->orderBy('created_at', 'desc');
        // return $this->statuses()
        //     ->orderBy('created_at', 'desc');
    }

    /**
     * 粉丝列表
     * 四个参数意思：
     *  1、目标model的class全称呼。
     *  2、中间表名
     *  3、中间表中当前model对应的关联字段
     *  4、中间表中目标model对应的关联字段
     *  5、用户表和粉丝表示同一张表，users
     * @return   [type]     [description]
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id')
            ->withPivot('created_at') //默认情况下，pivot 对象只包含两个关联模型的键。如果中间表里还有额外字段，则必须在定义关联时明确指出：
        // ->wherePivot('approved', 1)
        // ->wherePivotIn('priority', [1, 2])//通过中间表列过滤关系
            ->withTimestamps(); //如果您想让中间表自动维护 created_at 和 updated_at 时间戳，那么在定义关联时加上 withTimestamps 方法即可
    }

    //关注列表
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id')
        ->withTimestamps();
    }

    //在我们为用户和粉丝模型进行了多对多关联之后，便可以使用 Eloquent 模型为多对多提供的一系列简便的方法。如使用 attach 方法或 sync(不重复记录) 方法在中间表上创建一个多对多记录，使用 detach 方法在中间表上移除一个记录，创建和移除操作并不会影响到两个模型各自的数据，所有的数据变动都在 中间表 上进行
    //attach, sync, detach 这几个方法都允许传入 id 数组参数。
    //sync 方法会接收两个参数，第一个参数为要进行添加的 id，第二个参数则指明是否要移除其它不包含在关联的 id 数组中的 id，true 表示移除，false 表示不移除，默认值为 true。由于我们在关注一个新用户的时候，仍然要保持之前已关注用户的关注关系，因此不能对其进行移除，所以在这里我们选用 false

    //关注
    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }

        $this->followings()->sync($user_ids, false);
    }

    //取消关注
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    /**
     * array:1 [▼
          0 => array:10 [▼
            "id" => 1
            "name" => "Summer"
            "email" => "summer@example.com"
            "email_verified_at" => "2019-01-25 02:28:55"
            "created_at" => "1980-10-10 02:41:06"
            "updated_at" => "2019-01-25 02:28:55"
            "is_admin" => 1
            "activation_token" => null
            "activated" => 1
            "pivot" => array:4 [▼
              "follower_id" => 51
              "user_id" => 1
              "created_at" => "2019-01-29 08:50:00"
              "updated_at" => "2019-01-29 08:50:00"
            ]
          ]
        ]
     */
    //是否关注某用户
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}

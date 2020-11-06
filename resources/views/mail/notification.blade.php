<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>420portal-Notification</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800,900" rel="stylesheet">

</head>

<body style="background:#f1f1f1;padding-top:20px;padding-bottom:20px;">
    <center>
        <table class="" border="0" cellspacing="0" cellpadding="0" width="600"
            style="width:6.25in;background:#ffffff; border-collapse:collapse">
            <tbody>
                <tr>
                    <td height="10"></td>
                </tr>
                
                <tr>
                    <td style="padding-left:20px;" align="center">
                        <img src="{{asset('/imgs/logo.png')}}" style="width:200px;height:73px;" alt="" srcset="">
                    </td>
                </tr>
                <tr>
                    <td height="30"></td>
                </tr>
              
                <tr>
                    <td style="padding-left:20px;">
                        @php
                            if($mdata->media_type == 'portal' && $mdata->notifiable->portal_id) {
                                $receiver = $mdata->notifiable->portal->name ?? '';
                            } else {
                                $receiver = $mdata->user->name ?? '';
                            }
                        @endphp
                        <p style="margin:5px 0px 5px 0px;font-size:20px;color:#222;font-family: Montserrat;font-weight:500;">
                            Hello <span style="color:#EFA720;font-weight:600;">{{ $receiver }}</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td height="15"></td>
                </tr>
                <tr>
                    <td style="padding-left:20px;">
                        <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                            You have Received a Notification:
                        </p>                        
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:20px;">
                        @switch($mdata->type)
                            @case('comment')
                                <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                                    <a href="{{url($mdata->notifier->name ?? '/')}}" style="color:#EFA720;text-decoration:none;font-size:23px;font-weight:bold;">{{$mdata->notifier->name}}</a>
                                    Commented on your Media.
                                    <a href="{{url('/media/'.$mdata->notifiable_id)}}"><img src="{{asset('/imgs/hand.png')}}"></a>                            
                                </p>  
                                @break
                            @case('reply')
                                <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                                    <a href="{{url($mdata->notifier->name ?? '/')}}" style="color:#EFA720;text-decoration:none;font-size:23px;font-weight:bold;">{{$mdata->notifier->name}}</a>
                                    Replied to your Comment.
                                    <a href="{{url('/media/'.$mdata->notifiable_id)}}"><img src="{{asset('/imgs/hand.png')}}"></a>                            
                                </p>                                
                                @break
                            @case('reply_topic')
                                <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                                    <a href="{{url($mdata->notifier->name ?? '/')}}" style="color:#EFA720;text-decoration:none;font-size:23px;font-weight:bold;">{{$mdata->notifier->name}}</a>
                                    Commented on your Post. 
                                    @php
                                        if($mdata->notifiable->title) {
                                            $slug = $mdata->notifiable->slug ?? 'master_slug';
                                            $post_link = url('marijuana-forums/'.$slug.'/'.$mdata->notifiable_id);
                                        } else {
                                            $slug = $mdata->notifiable->get_m_parent->slug ?? 'master_slug';
                                            $post_link = url('marijuana-forums/'.$slug.'/'.$mdata->notifiable_id);
                                        }
                                    @endphp
                                    <a href="{{$post_link}}"><img src="{{asset('/imgs/hand.png')}}"></a>
                                </p>                                
                                @break
                            @case('reply_news')
                                <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                                    <a href="{{url($mdata->notifier->name ?? '/')}}" style="color:#EFA720;text-decoration:none;font-size:23px;font-weight:bold;">{{$mdata->notifier->name}}</a>
                                    Replied on your Comment. 
                                    <a href="{{url('/marijuana-news/'.$mdata->notifiable->slug ?? '/')}}"><img src="{{asset('/imgs/hand.png')}}"></a>                            
                                </p>                                
                                @break
                            @case('like')
                                <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                                    <a href="{{url($mdata->notifier->name ?? '/')}}" style="color:#EFA720;text-decoration:none;font-size:23px;font-weight:bold;">{{$mdata->notifier->name}}</a>
                                    Likes your Media. 
                                    <a href="{{url('/media/'.$mdata->notifiable_id)}}"><img src="{{asset('/imgs/hand.png')}}"></a>                            
                                </p>                                
                                @break
                            @case('like_comment')
                                <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                                    <a href="{{url($mdata->notifier->name ?? '/')}}" style="color:#EFA720;text-decoration:none;font-size:23px;font-weight:bold;">{{$mdata->notifier->name}}</a>
                                    Likes your Comment. 
                                    <a href="{{url($mdata->notifier->name ?? '/')}}"><img src="{{asset('/imgs/hand.png')}}"></a>                            
                                </p>
                                @break                            
                            @case('like_news_comment')
                                <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                                    <a href="{{url($mdata->notifier->name ?? '/')}}" style="color:#EFA720;text-decoration:none;font-size:23px;font-weight:bold;">{{$mdata->notifier->name}}</a>
                                    Likes your Comment. 
                                    <a href="{{url('/marijuana-news/'.$mdata->notifiable->slug ?? '/')}}"><img src="{{asset('/imgs/hand.png')}}"></a>                            
                                </p>
                                @break
                            @case('like_topic')
                                <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                                    <a href="{{url($mdata->notifier->name ?? '/')}}" style="color:#EFA720;text-decoration:none;font-size:23px;font-weight:bold;">{{$mdata->notifier->name}}</a>
                                    Likes your Post.  
                                    @php
                                        if($mdata->notifiable->title) {
                                            $slug = $mdata->notifiable->slug ?? 'master_slug';
                                            $post_link = url('marijuana-forums/'.$slug.'/'.$mdata->notifiable_id);
                                        } else {
                                            $slug = $mdata->notifiable->get_m_parent->slug ?? 'master_slug';
                                            $post_link = url('marijuana-forums/'.$slug.'/'.$mdata->notifiable_id);
                                        }
                                    @endphp
                                    <a href="{{$post_link}}"><img src="{{asset('/imgs/hand.png')}}"></a>                          
                                </p>
                                @break
                            @case('follow')
                                <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                                    <a href="{{url($mdata->notifier->name ?? '/')}}" style="color:#EFA720;text-decoration:none;font-size:23px;font-weight:bold;">{{$mdata->notifier->name}}</a>
                                    Started Following you. 
                                    <a href="{{url($mdata->notifier->name ?? '/')}}"><img src="{{asset('/imgs/hand.png')}}"></a>                            
                                </p>
                                @break
                            @case('follow_request')
                                <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                                    <a href="{{url($mdata->notifier->name ?? '/')}}" style="color:#EFA720;text-decoration:none;font-size:23px;font-weight:bold;">{{$mdata->notifier->name}}</a>
                                    Requested Following you. 
                                    <a href="{{url('/notification')}}"><img src="{{asset('/imgs/hand.png')}}"></a>                            
                                </p>
                                @break
                            @default                                
                        @endswitch                       
                    </td>
                </tr>
                <tr>
                    <td height="15"></td>
                </tr>
                <tr>
                    <td style="padding-left:20px;">
                        <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                            Remember, the More you Post, the More you'll Grow. 🌱
                        </p>                        
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:20px;">
                        <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                            Have fun!!                 
                        </p>                        
                    </td>
                </tr>
                <tr>
                    <td height="30"></td>
                </tr>
            </tbody>
        </table>


    </center>
</body>

</html>

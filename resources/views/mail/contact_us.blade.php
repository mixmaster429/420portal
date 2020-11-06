<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>420portal</title>
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
                        <img src="{{asset('imgs/logo.png')}}" style="width:200px;height:73px;" alt="" srcset="">
                    </td>
                </tr>
                <tr>
                    <td height="30"></td>
                </tr>
                <tr>
                    <td style="padding-left:20px;">
                        <p style="margin:5px 0px 5px 0px;font-size:20px;color:#222;font-family: Montserrat;font-weight:500;">
                            Hello
                        </p>
                    </td>
                </tr>
                <tr>
                    <td height="15"></td>
                </tr>
                @php
                    $user = Auth::user();
                @endphp
                <tr>
                    <td style="padding-left:20px;">
                        <p style="margin:5px 0px 5px 0px;font-size:18px;color:#222;font-family: Montserrat;font-weight:500;">
                            <a href="{{url($user->name)}}" style="color:#EFA720;text-decoration:none;font-size:23px;font-weight:bold;">{{$user->name}}</a>
                            wants to contact you. <a href="{{url($user->name)}}"><img src="{{asset('/imgs/hand.png')}}" /></a>
                        </p>                        
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:20px;white-space:pre-line;">
                        <p style="margin:5px 0px 5px 0px;font-size:20px;color:#222;font-family: Montserrat;font-weight:500;white-space:pre-line !important;">
                            {{$data[0]['message']}}
                        </p>
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

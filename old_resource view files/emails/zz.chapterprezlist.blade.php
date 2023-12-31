<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=320, target-densitydpi=device-dpi">
        <title>MOMS Club</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style type="text/css">
            /* Mobile-specific Styles */
            @media only screen and (max-width: 660px) { 
                .btn.btn-primary{background: #216498;border-color: #184e77;padding: 6px 15px;
                                    font-size: 14px;
                                    border-radius: 3px;
                                    transition: border .2s linear,color .2s linear,width .2s linear,background-color .2s linear;
                                    -webkit-font-smoothing: subpixel-antialiased;
                                }
            }
        </style>        
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                     <layout label="Text only">
                        <table class="w580" width="580" cellpadding="0" cellspacing="0" border="0">
                            <tbody>
                                <tr>
                                        <td class="w580" width="580">
                                            
                                            <div align="left" class="article-content">
                                                <div align="left" class="article-content">
                                                        <multiline label="Description">
                                                            {{$content}}
                                                            <br />
                                                        
                                                        </multiline>
                                                        <p><a href="mailto:{{$chapterPreEmail}}">{{$chapterPreEmail}}</a>,{{$chapterPreFname}} {{$chapterPreLname}}, {{$chapterName}}, {{$chapterState}}</p>
                                                        
                                                        
                                                </div>
                                            </div>
                                        </td>
                                </tr>
                                <tr>
                                    <td class="w580" width="580" height="10" align="left">                              
                                        <p><b>MCL</b>,<br><br>
                                        MIMI Database Administrator</p>
                                    </td>
                                </tr>
                             </tbody>
                        </table>
                    </layout> 
                </div>
            </div>
        </div>
    </body>
</html>

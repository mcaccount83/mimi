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
            .highllgt{
                background-color: #ffff00;
            }
            td{
                padding-left: 10px;
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
                                                        The MOMS Club of  has submitted their Board Election Report.
                                                        <table class="w580" width="580" cellpadding="0" cellspacing="0" border="1">
                                                            <thead>
                                                                <th></th>
                                                                <th>Submitted Information</th>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td></td>
                                                                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>President</b></center></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{$prefname}}</td>
                                                                    <td>{{$prelname}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{$preemail}}</td>
                                                                    <td>{{$prephone}}</td>
                                                                </tr>
                                                                    <td>{{$prestreet}}</td>
                                                                </tr>
                                                                    <td>{{$precity}}</td>
                                                                    <td>{{$prestate}}</td>
                                                                    <td>{{$prezip}}</td>
                                                                </tr>
                                                                
                                                                
                                                            </tbody>
                                                        </table>
                                                            </p>
                                                        
                                                        </multiline>
                                                        
                                                        
                                                        
                                                </div>
                                            </div>
                                        </td>
                                </tr>
                                <tr>
                                    <td class="w580" width="580" height="10" align="left">                              
                                        <p><b>MCL</b>,<br>
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
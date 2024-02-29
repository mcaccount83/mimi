<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=320, target-densitydpi=device-dpi">
        <title>MIMI</title>

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
                                       <p>Hello MOMS Club of {{$chapterName}}, {{$chapterState}}!</p>    
                                                        <p>Thank you for your group’s contribution of ${{$chapterAmount}} to the International MOMS Club’s Mother-To-Mother Fund!</p>

                                        <p>Your contribution will be added to the fund for use when a personal or natural disaster strikes MOMS Club members.  Please pass on to your members our congratulations for their generosity and compassion for their MOMS Club sisters.</p>

                                        <p>Because of their farsightedness in contributing to the Mother-To-Mother Fund now, emergency assistance will be available should a disaster strike a MOMS Club family in the future.</p>

                                        <p>Thank you again for helping!  Your members should be very proud of themselves!  I know we are very proud of all of you!</p><br />  
                                                        
                                                        
                                                        </multiline>
                                                       
                                                </div>
                                            </div>
                                        </td>
                                </tr>
                                <tr>
                                    <td class="w580" width="580" height="10" align="left">                              
                                        <p><b>MCL</b>,<br>
                                        Mother-To-Mother Fund Committee</p>
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
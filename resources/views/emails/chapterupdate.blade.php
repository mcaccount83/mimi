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
                                                            <p>Hello {{$cor_fnameUpd}}! <br/>
                                                        The MOMS Club of {{$chapterNameUpd}}, {{$chapterStateUpd}} has been updated through the MOMS Information Management Interface.
                                                        <table class="w580" width="580" cellpadding="0" cellspacing="0" border="1">
                                                            <thead>
                                                                <th></th>
                                                                <th>Previous Information</th>
                                                                <th>Updated Information</th>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td></td>
                                                                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>President</b></center></td>
                                                                </tr>
                                                                <tr class="{{$chapfnamePre != $chapfnameUpd ? 'highllgt' : ''}}">
                                                                    <td>First Name</td>
                                                                    <td>{{$chapfnamePre}}</td>
                                                                    <td>{{$chapfnameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$chaplnamePre != $chaplnameUpd ? 'highllgt' : ''}}">
                                                                    <td>Last Name</td>
                                                                    <td>{{$chaplnamePre}}</td>
                                                                    <td>{{$chaplnameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$chapteremailPre != $chapteremailUpd ? 'highllgt' : ''}}">
                                                                    <td>E-mail</td>
                                                                    <td>{{$chapteremailPre}}</td>
                                                                    <td>{{$chapteremailUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$streetPre != $streetUpd ? 'highllgt' : ''}}">
                                                                    <td>Street</td>
                                                                    <td>{{$streetPre}}</td>
                                                                    <td>{{$streetUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$cityPre != $cityUpd ? 'highllgt' : ''}}">
                                                                    <td>City</td>
                                                                    <td>{{$cityPre}}</td>
                                                                    <td>{{$cityUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$statePre != $stateUpd ? 'highllgt' : ''}}">
                                                                    <td>State</td>
                                                                    <td>{{$statePre}}</td>
                                                                    <td>{{$stateUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$zipPre != $zipUpd ? 'highllgt' : ''}}">
                                                                    <td>Zip</td>
                                                                    <td>{{$zipPre}}</td>
                                                                    <td>{{$zipUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$countryPre != $countryUpd ? 'highllgt' : ''}}">
                                                                    <td>Country</td>
                                                                    <td>{{$countryPre}}</td>
                                                                    <td>{{$countryUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$phonePre != $phoneUpd ? 'highllgt' : ''}}">
                                                                    <td>Phone</td>
                                                                    <td>{{$phonePre}}</td>
                                                                    <td>{{$phoneUpd}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td></td>
                                                                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>AVP</b></center></td>
                                                                </tr>
                                                                <tr class="{{$avpfnamePre != $avpfnameUpd ? 'highllgt' : ''}}">
                                                                    <td>First Name</td>
                                                                    <td>{{$avpfnamePre}}</td>
                                                                    <td>{{$avpfnameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$avplnamePre != $avplnameUpd ? 'highllgt' : ''}}">
                                                                    <td>Last Name</td>
                                                                    <td>{{$avplnamePre}}</td>
                                                                    <td>{{$avplnameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$avpemailPre != $avpemailUpd ? 'highllgt' : ''}}"> 
                                                                    <td>E-mail</td>
                                                                    <td>{{$avpemailPre}}</td>
                                                                    <td>{{$avpemailUpd}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td></td>
                                                                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>MVP</b></center></td>
                                                                </tr>
                                                                <tr class="{{$mvpfnamePre != $mvpfnameUpd ? 'highllgt' : ''}}">
                                                                    <td>First Name</td>
                                                                    <td>{{$mvpfnamePre}}</td>
                                                                    <td>{{$mvpfnameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$mvplnamePre != $mvplnameUpd ? 'highllgt' : ''}}">
                                                                    <td>Last Name</td>
                                                                    <td>{{$mvplnamePre}}</td>
                                                                    <td>{{$mvplnameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$mvpemailPre != $mvpemailPre ? 'highllgt' : ''}}">
                                                                    <td>E-mail</td>
                                                                    <td>{{$mvpemailPre}}</td>
                                                                    <td>{{$mvpemailUpd}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td></td>
                                                                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>Treasurer</b></center></td>
                                                                </tr>
                                                                <tr class="{{$tresfnamePre != $tresfnameUpd ? 'highllgt' : ''}}">
                                                                    <td>First Name</td>
                                                                    <td>{{$tresfnamePre}}</td>
                                                                    <td>{{$tresfnameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$treslnamePre != $treslnameUpd ? 'highllgt' : ''}}">
                                                                    <td>Last Name</td>
                                                                    <td>{{$treslnamePre}}</td>
                                                                    <td>{{$treslnameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$tresemailPre != $tresemailUpd ? 'highllgt' : ''}}">
                                                                    <td>E-mail</td>
                                                                    <td>{{$tresemailPre}}</td>
                                                                    <td>{{$tresemailUpd}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td></td>
                                                                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>Secretary</b></center></td>
                                                                </tr>
                                                                <tr class="{{$secfnamePre != $secfnameUpd ? 'highllgt' : ''}}">
                                                                    <td>First Name</td>
                                                                    <td>{{$secfnamePre}}</td>
                                                                    <td>{{$secfnameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$seclnamePre != $seclnameUpd ? 'highllgt' : ''}}">
                                                                    <td>Last Name</td>
                                                                    <td>{{$seclnamePre}}</td>
                                                                    <td>{{$seclnameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$secemailPre != $secemailUpd ? 'highllgt' : ''}}">
                                                                    <td>E-mail</td>
                                                                    <td>{{$secemailPre}}</td>
                                                                    <td>{{$secemailUpd}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td></td>
                                                                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>Chapter Fields</b></center></td>
                                                                </tr>
                                                                <tr class="{{$einPre != $einUpd ? 'highllgt' : ''}}">
                                                                    <td>EIN</td>
                                                                    <td>{{$einPre}}</td>
                                                                    <td>{{$einUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$einLetterPre != $einLetterUpd ? 'highllgt' : ''}}">
                                                                    <td>EIN Letter</td>
                                                                    <td>@if($einLetterPre!=null) Letter on File
                                                                    @else

                                                                     @endif   
                                                                    </td>
                                                                    <td>@if($einLetterUpd!=null) Letter on File
                                                                    @else

                                                                     @endif     </td>
                                                                </tr>
                                                                <tr class="{{$chapterNamePre != $chapterNameUpd ? 'highllgt' : ''}}">
                                                                    <td>Name</td>
                                                                    <td>{{$chapterNamePre}}</td>
                                                                    <td>{{$chapterNameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$statePre != $stateUpd ? 'highllgt' : ''}}">
                                                                    <td>State</td>
                                                                    <td>{{$statePre}}</td>
                                                                    <td>{{$stateUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$inConPre != $inConUpd ? 'highllgt' : ''}}">
                                                                    <td>Inquiries Contact</td>
                                                                    <td>{{$inConPre}}</td>
                                                                    <td>{{$inConUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$inNotePre != $inNoteUpd ? 'highllgt' : ''}}">
                                                                    <td>Inquiries Notes</td>
                                                                    <td>{{$inNotePre}}</td>
                                                                    <td>{{$inNoteUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$chapemailPre != $chapemailUpd ? 'highllgt' : ''}}">
                                                                    <td>Chapter E-mail</td>
                                                                    <td>{{$chapemailPre}}</td>
                                                                    <td>{{$chapemailUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$poBoxPre != $poBoxUpd ? 'highllgt' : ''}}">
                                                                    <td>PO Box</td>
                                                                    <td>{{$poBoxPre}}</td>
                                                                    <td>{{$poBoxUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$webUrlPre != $webUrlUpd ? 'highllgt' : ''}}">
                                                                    <td>Website URL</td>
                                                                    <td>{{$webUrlPre}}</td>
                                                                    <td>{{$webUrlUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$weblinkStatusPre != $weblinkStatusUpd ? 'highllgt' : ''}}">
                                                                    <td>Website Link Status</td>
                                                                    <td>@if($weblinkStatusPre==1)

                                                                            Linked
                                                                            @elseif($weblinkStatusPre==2)
                                                                            Link Requested
                                                                            @else
                                                                            Do Not Link
                                                                        @endif
                                                                    </td>
                                                                    <td>@if($weblinkStatusUpd==1)

                                                                            Linked
                                                                            @elseif($weblinkStatusUpd==2)
                                                                            Link Requested
                                                                            @else
                                                                            Do Not Link
                                                                        @endif</td>
                                                                </tr>
                                                                <tr class="{{$egroupPre != $egroupUpd ? 'highllgt' : ''}}">
                                                                    <td>E-Group</td>
                                                                    <td>{{$egroupPre}}</td>
                                                                    <td>{{$egroupUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$boundPre != $boundUpd ? 'highllgt' : ''}}">
                                                                    <td>Chapter Boundaries</td>
                                                                    <td>{{$boundPre}}</td>
                                                                    <td>{{$boundUpd}}</td>
                                                                </tr>
                                                                <tr class="{{($cor_fnamePre != $cor_fnameUpd) && ($cor_lnamePre != $cor_lnameUpd) ? 'highllgt' : ''}}">
                                                                    <td>Primary Coordinator</td>
                                                                    <td>{{$cor_fnamePre}} {{$cor_lnamePre}}</td>
                                                                    <td>{{$cor_fnameUpd}} {{$cor_lnameUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$addInfoPre != $addInfoUpd ? 'highllgt' : ''}}">
                                                                    <td>Additional Information</td>
                                                                    <td>{{$addInfoPre}}</td>
                                                                    <td>{{$addInfoUpd}}</td>
                                                                </tr>
                                                                <tr class="{{$chapstatusPre != $chapstatusUpd ? 'highllgt' : ''}}">
                                                                    <td>Chapter Status</td>
                                                                    <td>@if($chapstatusPre==1) Operating Ok
                                                                    @elseif($chapstatusPre==4)
                                                                    On Hold Do Not Refer
                                                                    @elseif($chapstatusPre==5) 
                                                                    Probation
                                                                     @elseif($chapstatusPre==6)
                                                                     Probation Do Not Refer 
                                                                     @endif   
                                                                    </td>
                                                                    <td>@if($chapstatusUpd==1) Operating Ok
                                                                    @elseif($chapstatusUpd==4)
                                                                    On Hold Do Not Refer
                                                                    @elseif($chapstatusUpd==5) 
                                                                    Probation
                                                                     @elseif($chapstatusUpd==6)
                                                                     Probation Do Not Refer 
                                                                     @endif   </td>
                                                                </tr>
                                                                <tr class="{{$chapNotePre != $chapNoteUpd ? 'highllgt' : ''}}">
                                                                    <td>Status Notes</td>
                                                                    <td>{{$chapNotePre}}</td>
                                                                    <td>{{$chapNoteUpd}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <!--<br>This was updated by: {{$cor_fnameUpd}} {{$cor_lnameUpd}} at {{$updated_byUpd}}.-->
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

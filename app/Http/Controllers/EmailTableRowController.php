<?php

namespace App\Http\Controllers;

class EmailTableRowController extends Controller
{
    protected $baseMailDataController;

    public function __construct(BaseMailDataController $baseMailDataController)
    {
        $this->baseMailDataController = $baseMailDataController;
    }

    /**
     * Create Rows for Founder Email
     */
    public function createFounderRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td colspan="2" style="'.$headerStyle.'">Founder Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'">Name</td>
            <td style="'.$headerStyle.'">Email</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellStyle.'">'.$mailData['presName'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['presEmail'].'</td>
        </tr>';

        return $tableHtml;
    }

    /**
     * Create Rows for Primary Coord Email
     */
    public function createPrimaryCoordEmailRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">Primary Coordinator</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['pcNameUpd'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['pcEmailUpd'].'</td>
        </tr>';

        return $tableHtml;
    }


    /**
     * Create Rows for President Email
     */
    public function createPresidentEmailRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">President Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['presName'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['presEmail'].'</td>
        </tr>';

        return $tableHtml;
    }

    /**
     * Create Rows for Founder Details
     */
 public function createFounderDetailRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">Chapter & Founder Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'">Chapter Name</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['chapterName'].' '.$mailData['chapterState'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Founder Name</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['presName'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Address</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['presAddress'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'"></td>
            <td style="'.$cellLeftStyle.'">'.$mailData['presCity'].', '.$mailData['presState'].' '.$mailData['presZip'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'"></td>
            <td style="'.$cellLeftStyle.'">'.$mailData['presCountry'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['presEmail'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Phone</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['presPhone'].'</td>
        </tr>';

        return $tableHtml;
    }

    /**
     * Create Rows for Chapter Email
     */
    public function createNewChapterEmailRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">Chapter Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'">Chapter Name</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['chapterName'].' '.$mailData['chapterState'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['chapterNameSanitized'].'.'.$mailData['chapterState'].'@momsclub.org</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Secondary Email</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['presEmail'].'</td>
        </tr>';

        return $tableHtml;
    }

    /**
     * Create Rows for Coordinator Application
     */
    public function createNewCoordAppRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">Application Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Volunteer Name</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['first_name'].' '.$mailData['last_name'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['sec_email'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Phone</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['phone'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Home Chapter</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['home_chapter'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">How long have you been a MOMS Club Member?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['start_date'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">What jobs/offices have you held with the chapter? What programs/activities have you started or led?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['jobs_programs'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">How has the MOMS Club helped you?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['helped_me'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Did you experience any problems during your time in the MOMS Club? If so, how were those problems resolved or what did you learn from them?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['problems'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Why do you want to be an International MOMS Club Volunteer?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['why_volunteer'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Do you volunteer for anyone else? Please list all your volunteer positions and when you did them?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['other_volunteer'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Do you have any special skills/talents/Hobbies (ie: other languages, proficient in any computer programs)?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['special_skills'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">What have you enjoyed most in previous volunteer experiences? Least?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['enjoy_volunteering'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Referred by: (if applicable):</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['referred_by'].'</td>
        </tr>';

        return $tableHtml;
    }

/**
     * Create Rows for Coordinator Details
     */
    public function createNewCoordinatorDetailRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">Coordinator Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Conference</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['conference_id'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Region</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['region'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['first_name'].' '.$mailData['last_name'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">MOMS Club Email</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['email'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Secondary Email</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['sec_email'].'</td>
        </tr>';

        return $tableHtml;
    }

    /**
     * Create Rows for Full Board
     */
    public function createBoardEmailRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td colspan="2" style="'.$headerStyle.'">Board Member Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td style="'.$headerStyle.'">Name</td>
            <td style="'.$headerStyle.'">Email</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">President</td>
            <td style="'.$cellStyle.'">'.$mailData['presName'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['presEmail'].'</td>
        </tr>';

        if (!empty($mailData['avpName'])) {
            $tableHtml .= '<tr>
                <td style="'.$cellLeftStyle.'">AVP</td>
                <td style="'.$cellStyle.'">'.$mailData['avpName'].'</td>
                <td style="'.$cellStyle.'">'.$mailData['avpEmail'].'</td>
            </tr>';
        }
          if (!empty($mailData['avpName'])) {
            $tableHtml .= '<tr>
                <td style="'.$cellLeftStyle.'">AVP</td>
                <td style="'.$cellStyle.'">'.$mailData['avpName'].'</td>
                <td style="'.$cellStyle.'">'.$mailData['avpEmail'].'</td>
            </tr>';
        }
         if (!empty($mailData['mvpName'])) {
            $tableHtml .= '<tr>
                <td style="'.$cellLeftStyle.'">AVP</td>
                <td style="'.$cellStyle.'">'.$mailData['mvpName'].'</td>
                <td style="'.$cellStyle.'">'.$mailData['mvpEmail'].'</td>
            </tr>';
        }
         if (!empty($mailData['trsName'])) {
            $tableHtml .= '<tr>
                <td style="'.$cellLeftStyle.'">AVP</td>
                <td style="'.$cellStyle.'">'.$mailData['trsName'].'</td>
                <td style="'.$cellStyle.'">'.$mailData['trsEmail'].'</td>
            </tr>';
        }
         if (!empty($mailData['secName'])) {
            $tableHtml .= '<tr>
                <td style="'.$cellLeftStyle.'">AVP</td>
                <td style="'.$cellStyle.'">'.$mailData['secName'].'</td>
                <td style="'.$cellStyle.'">'.$mailData['secEmail'].'</td>
            </tr>';
        }

        return $tableHtml;
    }

    /**
     * Create Rows for Board Member Updates
     */
    public function createMemberUpdateRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td colspan="2" style="'.$headerStyle.'">'.$mailData['borPosition'].' Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'"></td>
            <td style="'.$headerStyle.'">Previous</td>
            <td style="'.$headerStyle.'">Updated</td>
        </tr>';
        $borNameStyle = ($mailData['boardName'] != $mailData['borNameUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$borNameStyle.'">
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellStyle.'">'.$mailData['boardName'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['borNameUpd'].'</td>
        </tr>';
        $borEmailStyle = ($mailData['boardEmail'] != $mailData['borEmailUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$borEmailStyle.'">
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellStyle.'">'.$mailData['boardEmail'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['borEmailUpd'].'</td>
        </tr>';

        return $tableHtml;
    }


    /**
     * Create Rows for Full Board Updates
     */
    public function createBoardUpdateRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td colspan="2" style="'.$headerStyle.'">President Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td style="'.$headerStyle.'">Previous</td>
            <td style="'.$headerStyle.'">Updated</td>
        </tr>';
        $presNameStyle = ($mailData['presName'] != $mailData['presNameUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$presNameStyle.'">
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellStyle.'">'.$mailData['presName'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['presNameUpd'].'</td>
        </tr>';
        $presEmailStyle = ($mailData['presEmail'] != $mailData['presEmailUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$presEmailStyle.'">
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellStyle.'">'.$mailData['presEmail'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['presEmailUpd'].'</td>
        </tr>';

        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td colspan="2" style="'.$headerStyle.'">AVP Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td style="'.$headerStyle.'">Previous</td>
            <td style="'.$headerStyle.'">Updated</td>
        </tr>';
        $avpNameStyle = ($mailData['avpName'] != $mailData['avpNameUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$avpNameStyle.'">
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellStyle.'">'.$mailData['avpName'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['avpNameUpd'].'</td>
        </tr>';
        $avpEmailStyle = ($mailData['avpemail'] != $mailData['avpemailUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$avpEmailStyle.'">
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellStyle.'">'.$mailData['avpemail'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['avpemailUpd'].'</td>
        </tr>';

        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td colspan="2" style="'.$headerStyle.'">MVP Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td style="'.$headerStyle.'">Previous</td>
            <td style="'.$headerStyle.'">Updated</td>
        </tr>';
        $mvpNameStyle = ($mailData['mvpName'] != $mailData['mvpNameUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$mvpNameStyle.'">
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellStyle.'">'.$mailData['mvpName'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['mvpNameUpd'].'</td>
        </tr>';
        $mvpEmailStyle = ($mailData['mvpemail'] != $mailData['mvpemailUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$mvpEmailStyle.'">
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellStyle.'">'.$mailData['mvpemail'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['mvpemailUpd'].'</td>
        </tr>';

        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td colspan="2" style="'.$headerStyle.'">Treasurer Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td style="'.$headerStyle.'">Previous</td>
            <td style="'.$headerStyle.'">Updated</td>
        </tr>';
        $tresNameStyle = ($mailData['tresName'] != $mailData['tresNameUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$tresNameStyle.'">
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellStyle.'">'.$mailData['tresName'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['tresNameUpd'].'</td>
        </tr>';
        $tresEmailStyle = ($mailData['tresemail'] != $mailData['tresemailUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$tresEmailStyle.'">
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellStyle.'">'.$mailData['tresemail'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['tresemailUpd'].'</td>
        </tr>';

        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td colspan="2" style="'.$headerStyle.'">Secretary Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td style="'.$headerStyle.'">Previous</td>
            <td style="'.$headerStyle.'">Updated</td>
        </tr>';
        $tresNameStyle = ($mailData['secName'] != $mailData['secNameUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$tresNameStyle.'">
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellStyle.'">'.$mailData['secName'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['secNameUpd'].'</td>
        </tr>';
        $tresEmailStyle = ($mailData['secemail'] != $mailData['secemailUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$tresEmailStyle.'">
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellStyle.'">'.$mailData['secemail'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['secemailUpd'].'</td>
        </tr>';

        return $tableHtml;
    }

    /**
     * Create Rows for General Chapter Updates
     */
    public function createChapterUpdateRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td colspan="2" style="'.$headerStyle.'">Chapter Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td style="'.$headerStyle.'">Previous</td>
            <td style="'.$headerStyle.'">Updated</td>
        </tr>';
        $chNameStyle = ($mailData['chapterName'] != $mailData['chapterNameUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chNameStyle.'">
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellStyle.'">'.$mailData['chapterName'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['chapterNameUpd'].'</td>
        </tr>';
        $chEmailStyle = ($mailData['chapterEmail'] != $mailData['chapterEmailUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chEmailStyle.'">
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellStyle.'">'.$mailData['chapterEmail'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['chapterEmailUpd'].'</td>
        </tr>';
        $chBoundariesStyle = ($mailData['chapterBoundaries'] != $mailData['boundariesUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chBoundariesStyle.'">
            <td style="'.$cellLeftStyle.'">Boundaries</td>
            <td style="'.$cellStyle.'">'.$mailData['chapterBoundaries'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['boundariesUpd'].'</td>
        </tr>';
        $chInquiriesStyle = ($mailData['chapterInquiriesContact'] != $mailData['inquiriesContactUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chInquiriesStyle.'">
            <td style="'.$cellLeftStyle.'">Inquiries</td>
            <td style="'.$cellStyle.'">'.$mailData['chapterInquiriesContact'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['inquiriesContactUpd'].'</td>
        </tr>';
        $chStatusStyle = ($mailData['chapterStatus'] != $mailData['statusUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chStatusStyle.'">
            <td style="'.$cellLeftStyle.'">Status</td>
            <td style="'.$cellStyle.'">'.$mailData['chapterStatus'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['statusUpd'].'</td>
        </tr>';
        $chNotesStyle = ($mailData['chapterNotes'] != $mailData['notesUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chNotesStyle.'">
            <td style="'.$cellLeftStyle.'">Notes</td>
            <td style="'.$cellStyle.'">'.$mailData['chapterNotes'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['notesUpd'].'</td>
        </tr>';

        return $tableHtml;
    }

    /**
     * Create Rows for Online/Website Updates
     */
    public function createWebsiteUpdateRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td colspan="2" style="'.$headerStyle.'">Online Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$headerStyle.'"></td>
            <td style="'.$headerStyle.'">Previous</td>
            <td style="'.$headerStyle.'">Updated</td>
        </tr>';
        $chNameStyle = ($mailData['chapterWebsiteURL'] != $mailData['websiteURLUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chNameStyle.'">
            <td style="'.$cellLeftStyle.'">Website</td>
            <td style="'.$cellStyle.'">'.$mailData['chapterWebsiteURL'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['websiteURLUpd'].'</td>
        </tr>';
        $chNameStyle = ($mailData['chapterWebsiteStatus'] != $mailData['websiteStatusUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chNameStyle.'">
            <td style="'.$cellLeftStyle.'">Status</td>
            <td style="'.$cellStyle.'">'.$mailData['chapterWebsiteStatus'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['websiteStatusUpd'].'</td>
        </tr>';
        $chNameStyle = ($mailData['egroup'] != $mailData['egroupUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chNameStyle.'">
            <td style="'.$cellLeftStyle.'">MeetUp</td>
            <td style="'.$cellStyle.'">'.$mailData['egroup'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['egroupUpd'].'</td>
        </tr>';
        $chNameStyle = ($mailData['facebook'] != $mailData['facebookUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chNameStyle.'">
            <td style="'.$cellLeftStyle.'">Facebook</td>
            <td style="'.$cellStyle.'">'.$mailData['facebook'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['facebookUpd'].'</td>
        </tr>';
        $chNameStyle = ($mailData['twitter'] != $mailData['twitterUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chNameStyle.'">
            <td style="'.$cellLeftStyle.'">Twitter</td>
            <td style="'.$cellStyle.'">'.$mailData['twitter'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['twitterUpd'].'</td>
        </tr>';
        $chNameStyle = ($mailData['instagram'] != $mailData['instagramUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$chNameStyle.'">
            <td style="'.$cellLeftStyle.'">Instagram</td>
            <td style="'.$cellStyle.'">'.$mailData['instagram'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['instagramUpd'].'</td>
        </tr>';

        return $tableHtml;
    }




}

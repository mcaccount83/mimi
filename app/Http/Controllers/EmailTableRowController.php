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
     * Create Rows for Mentoring Coord Email
     */
    public function createMentoringCoordEmailRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">Mentoring Coordinator</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['rcName'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['rcEmail'].'</td>
        </tr>';

        return $tableHtml;
    }

       /**
     * Create Rows for Mentoring Coord Email
     */
    public function createCoordEmailRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">Coordinator Details</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['cdName'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['cdEmail'].'</td>
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
            <td style="'.$cellLeftStyle.'">Chapter Name</td>
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
            <td style="'.$cellLeftStyle.'">Chapter Name</td>
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

        if (! empty($mailData['avpName'])) {
            $tableHtml .= '<tr>
                <td style="'.$cellLeftStyle.'">AVP</td>
                <td style="'.$cellStyle.'">'.$mailData['avpName'].'</td>
                <td style="'.$cellStyle.'">'.$mailData['avpEmail'].'</td>
            </tr>';
        }
        if (! empty($mailData['avpName'])) {
            $tableHtml .= '<tr>
                <td style="'.$cellLeftStyle.'">AVP</td>
                <td style="'.$cellStyle.'">'.$mailData['avpName'].'</td>
                <td style="'.$cellStyle.'">'.$mailData['avpEmail'].'</td>
            </tr>';
        }
        if (! empty($mailData['mvpName'])) {
            $tableHtml .= '<tr>
                <td style="'.$cellLeftStyle.'">AVP</td>
                <td style="'.$cellStyle.'">'.$mailData['mvpName'].'</td>
                <td style="'.$cellStyle.'">'.$mailData['mvpEmail'].'</td>
            </tr>';
        }
        if (! empty($mailData['trsName'])) {
            $tableHtml .= '<tr>
                <td style="'.$cellLeftStyle.'">AVP</td>
                <td style="'.$cellStyle.'">'.$mailData['trsName'].'</td>
                <td style="'.$cellStyle.'">'.$mailData['trsEmail'].'</td>
            </tr>';
        }
        if (! empty($mailData['secName'])) {
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
            <td style="'.$headerStyle.'"></td>
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
        $avpEmailStyle = ($mailData['avpEmail'] != $mailData['avpEmailUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$avpEmailStyle.'">
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellStyle.'">'.$mailData['avpEmail'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['avpEmailUpd'].'</td>
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
        $mvpEmailStyle = ($mailData['mvpEmail'] != $mailData['mvpEmailUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$mvpEmailStyle.'">
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellStyle.'">'.$mailData['mvpEmail'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['mvpEmailUpd'].'</td>
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
        $trsNameStyle = ($mailData['trsName'] != $mailData['trsNameUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$trsNameStyle.'">
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellStyle.'">'.$mailData['trsName'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['trsNameUpd'].'</td>
        </tr>';
        $trsEmailStyle = ($mailData['trsEmail'] != $mailData['trsEmailUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$trsEmailStyle.'">
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellStyle.'">'.$mailData['trsEmail'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['trsEmailUpd'].'</td>
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
        $secNameStyle = ($mailData['secName'] != $mailData['secNameUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$secNameStyle.'">
            <td style="'.$cellLeftStyle.'">Name</td>
            <td style="'.$cellStyle.'">'.$mailData['secName'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['secNameUpd'].'</td>
        </tr>';
        $secEmailStyle = ($mailData['secEmail'] != $mailData['secEmailUpd']) ? 'background-color: yellow;' : '';
        $tableHtml .= '<tr style="'.$secEmailStyle.'">
            <td style="'.$cellLeftStyle.'">Email</td>
            <td style="'.$cellStyle.'">'.$mailData['secEmail'].'</td>
            <td style="'.$cellStyle.'">'.$mailData['secEmailUpd'].'</td>
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

      public function createNewGrantRows($mailData, $cellStyle, $cellLeftStyle, $headerStyle, $tableHtml)
    {
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">Grant Information</td>
        </tr>';
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$cellLeftStyle.'">I have read this section and understand the limits of the fund: '.($mailData['understood'] == '1' ? 'YES' : 'NO').'</td>
        </tr>';
         $tableHtml .= '<tr>
            <td colspan="2" style="'.$cellLeftStyle.'">I have read this section and understand the limits of the fund: '.($mailData['member_agree'] == '1' ? 'YES' : 'NO').'</td>
        </tr>';
         $tableHtml .= '<tr>
            <td colspan="2" style="'.$cellLeftStyle.'">I have read this section and understand the limits of the fund: '.($mailData['member_accept'] == '1' ? 'YES' : 'NO').'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">MEMBER IN NEED</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Member Name</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['first_name'].' '.$mailData['last_name'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Address</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['address'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'"></td>
            <td style="'.$cellLeftStyle.'">'.$mailData['city'].', '.$mailData['state'].' '.$mailData['zip'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'"></td>
            <td style="'.$cellLeftStyle.'">'.$mailData['country'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">How long has the mother been a member?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['member_length'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Who is living in the home?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['household_members'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">EXPLANAION OF SITUATION</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Please provide a summary of the situation./td>
            <td style="'.$cellLeftStyle.'">'.$mailData['situation_summary'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">What has the family done to improve or handle the situation?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['family_actions'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">What is the financial situation of the family?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['financial_situation'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">What are the familys most pressing needs right now?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['pressing_needs'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Is there anything else the family needs?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['other_needs'].'</td>
        </tr>';
         $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">GRANT REQUEST DETAILS</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">What amount is being requested? What will it be used for?/td>
            <td style="'.$cellLeftStyle.'">'.$mailData['amount_requested'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">How has the chapter supported the member up to this point?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['chapter_support'].'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td style="'.$cellLeftStyle.'">Is there anything else we should know?</td>
            <td style="'.$cellLeftStyle.'">'.$mailData['additional_info'].'</td>
        </tr>';
         $tableHtml .= '<tr>
            <td colspan="2" style="'.$headerStyle.'">CHAPTER BACKING AND AFFIRMATION</td>
        </tr>';
         $tableHtml .= '<tr>
            <td colspan="2" style="'.$cellLeftStyle.'">Has the chapter ever asked for a grant for this mother or family in the past? '.($mailData['previous_grant'] == '1' ? 'YES' : 'NO').'</td>
        </tr>';
         $tableHtml .= '<tr>
            <td colspan="2" style="'.$cellLeftStyle.'">Does the chapter stand behind this request? '.($mailData['chapter_backing'] == '1' ? 'YES' : 'NO').'</td>
        </tr>';
         $tableHtml .= '<tr>
            <td colspan="2" style="'.$cellLeftStyle.'">Has the chapter donated to the Mother-to-Mother Fund? '.($mailData['m2m_donation'] == '1' ? 'YES' : 'NO').'</td>
        </tr>';
        $tableHtml .= '<tr>
            <td colspan="2" style="'.$cellLeftStyle.'">I affirm that the information in this submission is true: '.($mailData['affirmation'] == '1' ? 'YES' : 'NO').'</td>
        </tr>';

        return $tableHtml;
    }
}

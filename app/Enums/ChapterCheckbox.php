<?php

namespace App\Enums;

class ChapterCheckbox
{
    // Chapter List Checkboxes
    const PRIMARY_COORDINATOR = 'check';      // Only show chapters I am primary for

    const REVIEWER = 'check2';                // Show chapters where I am reviewer

    const CONFERENCE_REGION = 'check3';       // Show all chapters in conference/region (based on position)

    const PROBATION = 'check4';               // Show chapters on probation

    const INTERNATIONAL = 'check5';           // Show all international chapters

    const INTERNATIONALREREG = 'check6';      // Show all international chapters with Re-Reg due

    const INQUIRIES = 'check7';                 // Show all outstaning inquiries

    const INTERNATIONALINQUIRIES = 'check8';    // Show all international outstaning inquiries

    // Checkbox Keys (for return array)
    const CHECK_PRIMARY = 'checkBoxStatus';

    const CHECK_REVIEWER = 'checkBox2Status';

    const CHECK_CONFERENCE_REGION = 'checkBox3Status';

    const CHECK_PROBATION = 'checkBox4Status';

    const CHECK_INTERNATIONAL = 'checkBox5Status';

    const CHECK_INTERNATIONALREREG = 'checkBox6Status';

    const CHECK_INQUIRIES = 'checkBox7Status';

    const CHECK_INTERNATIONALINQUIRIES = 'checkBox8Status';
}

<?php

namespace App\Enums;

class CheckboxFilterEnum
{
    // Chapter/Coordinator List Checkboxes
    const PC_DIRECT = 'check1';                 // Only show chapters I am primary/coordinators I am directly supervisor for
    const REVIEWER = 'check2';                  // Show chapters where I am EOY reviewer
    const CONFERENCE_REGION = 'check3';         // Show all chapters/coordinators in conference/region (based on position)

    // Specialty Job List Checkboxes
    const LIST = 'check5';
    const REREG = 'check6';
    const INQUIRIES = 'check7';
    const M2M = 'check8';
    const EIN = 'check9';

    // Internatioal List Checkboxes
    const INTERNATIONAL = 'check51';
    const INTERNATIONALREREG = 'check56';
    const INTERNATIONALINQUIRIES = 'check57';
    const INTERNATIONALM2M = 'check58';
    const INTERNATIONALEIN = 'check59';

    // Misc/Admin List Checkboxes
    const ADMIN = 'check81';
    const REPORTING_TREE = 'check86';           // Show Mary's full reporting tree
}

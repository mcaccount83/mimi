<?php

namespace App\Enums;

class CheckboxFilterEnum
{
    // Chapter/Coordinator List Checkboxes
    const PC_DIRECT = 'check1';                 // Only show chapters I am primary/coordinators I am directly supervisor for
    const REVIEWER = 'check2';                  // Show chapters where I am reviewer
    const CONFERENCE_REGION = 'check3';         // Show all chapters/coordinators in conference/region (based on position)

    // Specialty Job List Checkboxes
    const INQUIRIES = 'check7';                 // Show all outstaning inquiries
    const M2MDONATIONS = 'check8';              // Show m2m donations only

    // Internatioal List Checkboxes
    const INTERNATIONAL = 'check51';            // Show all international chapters/coordinators
    const INTERNATIONALREREG = 'check56';       // Show all international chapters with Re-Reg due
    const INTERNATIONALINQUIRIES = 'check57';       // Show all international outstaning inquiries
    const INTERNATIONALM2MDONATIONS = 'check58';    // Show all international m2m donations

    // Misc/Admin List Checkboxes
    const ADMIN = 'check81';                    // Show content based on admin status
    const REPORTING_TREE = 'check86';           // Show Mary's full reporting tree
}

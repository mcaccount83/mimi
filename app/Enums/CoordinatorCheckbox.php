<?php

namespace App\Enums;

class CoordinatorCheckbox
{
    // Coordinator List Checkboxes
    const DIRECT_REPORT = 'check';      // Only show coordinators I am directly supervisor for
    // const REVIEWER = 'check2';
    const CONFERENCE_REGION = 'check3';       // Show all coordinators in conference/region (based on position)
    // const PROBATION = 'check4';
    const INTERNATIONAL = 'check5';           // Show all international coordinators
        const REPORTING_TREE = 'check6';          // Show Mary's full reporting tree


    // Checkbox Keys (for return array)
    const CHECK_DIRECT = 'checkBoxStatus';
    // const CHECK_REVIEWER = 'checkBox2Status';
    const CHECK_CONFERENCE_REGION = 'checkBox3Status';
    // const CHECK_PROBATION = 'checkBox4Status';
    const CHECK_INTERNATIONAL = 'checkBox5Status';
        const CHECK_REPORTING_TREE = 'checkBox6Status';

}

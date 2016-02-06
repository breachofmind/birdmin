<?php

if (!function_exists('get_state_array'))
{
    /**
     * Get an array of states / abbrevs.
     * @return array
     */
    function get_state_array ()
    {
        return array (
            "AL"=>"Alabama",
            "AK"=>"Alaska",
            "AZ"=>"Arizona",
            "AR"=>"Arkansas",
            "CA"=>"California",
            "CO"=>"Colorado",
            "CT"=>"Connecticut",
            "DE"=>"Delaware",
            "DC"=>"District Of Columbia",
            "FL"=>"Florida",
            "GA"=>"Georgia",
            "HI"=>"Hawaii",
            "ID"=>"Idaho",
            "IL"=>"Illinois",
            "IN"=>"Indiana",
            "IA"=>"Iowa",
            "KS"=>"Kansas",
            "KY"=>"Kentucky",
            "LA"=>"Louisiana",
            "ME"=>"Maine",
            "MD"=>"Maryland",
            "MA"=>"Massachusetts",
            "MI"=>"Michigan",
            "MN"=>"Minnesota",
            "MS"=>"Mississippi",
            "MO"=>"Missouri",
            "MT"=>"Montana",
            "NE"=>"Nebraska",
            "NV"=>"Nevada",
            "NH"=>"New Hampshire",
            "NJ"=>"New Jersey",
            "NM"=>"New Mexico",
            "NY"=>"New York",
            "NC"=>"North Carolina",
            "ND"=>"North Dakota",
            "OH"=>"Ohio",
            "OK"=>"Oklahoma",
            "OR"=>"Oregon",
            "PA"=>"Pennsylvania",
            "RI"=>"Rhode Island",
            "SC"=>"South Carolina",
            "SD"=>"South Dakota",
            "TN"=>"Tennessee",
            "TX"=>"Texas",
            "UT"=>"Utah",
            "VT"=>"Vermont",
            "VA"=>"Virginia",
            "WA"=>"Washington",
            "WV"=>"West Virginia",
            "WI"=>"Wisconsin",
            "WY"=>"Wyoming",
        );
    }
    /**
     * Get the abbreviation or name of state.
     * @param string $abbr_or_name - can be abbreviation or name of a state.
     * @param boolean $abbrev - return the abbreviation or name?
     * @return boolean|string
     */
    function get_state ($abbr_or_name, $abbrev=true)
    {
        $states = get_state_array();

        // Need to pass something.
        if (empty($abbr_or_name)) {
            return false;
        }

        // The user passed an abbreviation.
        if (array_key_exists($abbr_or_name, $states)) {
            return $abbrev ? $abbr_or_name : $states[$abbr_or_name];

            // The user passed the name.
        } else {
            foreach ($states as $key=>$value) {
                if (strcasecmp ($abbr_or_name, $value)==0) {
                    return $abbrev ? $key : $value;
                }
            }
        }

        return false;
    }
}
<?php

// set assignments of important variables
define(SE_SUN, 0);
define(SE_MOON, 1);
define(SE_MERCURY, 2);
define(SE_VENUS, 3);
define(SE_MARS, 4);
define(SE_JUPITER, 5);
define(SE_SATURN, 6);
define(SE_TNNODE, 7);
define(SE_TSNODE, 8);		//this must be last thing before angle stuff

define(LAST_PLANET, 8);

$moiety[0] = 8.5;			//planet moiety (1/2 the orb)
$moiety[1] = 6.25;
$moiety[2] = 3.5;
$moiety[3] = 4;
$moiety[4] = 3.75;
$moiety[5] = 6;
$moiety[6] = 5;
$moiety[7] = 0;
$moiety[8] = 0;

$pl_name[0] = "Sun";
$pl_name[1] = "Moon";
$pl_name[2] = "Mercury";
$pl_name[3] = "Venus";
$pl_name[4] = "Mars";
$pl_name[5] = "Jupiter";
$pl_name[6] = "Saturn";
$pl_name[7] = "N. Node";
$pl_name[8] = "S. Node";

$pl_glyph[0] = 81;
$pl_glyph[1] = 87;
$pl_glyph[2] = 69;
$pl_glyph[3] = 82;
$pl_glyph[4] = 84;
$pl_glyph[5] = 89;
$pl_glyph[6] = 85;
$pl_glyph[7] = 141;
$pl_glyph[8] = 142;

$pl_glyph[LAST_PLANET + 1] = 90;		//Ascendant
$pl_glyph[LAST_PLANET + 2] = 88;		//Midheaven


$pl_ephem_number[0] = "0";
$pl_ephem_number[1] = "1";
$pl_ephem_number[2] = "2";
$pl_ephem_number[3] = "3";
$pl_ephem_number[4] = "4";
$pl_ephem_number[5] = "5";
$pl_ephem_number[6] = "6";

$pl_name[LAST_PLANET + 1] = "Ascendant";
$pl_name[LAST_PLANET + 2] = "Midheaven";

$pl_name[LAST_PLANET + 1] = "Ascendant";
$pl_name[LAST_PLANET + 2] = "House 2";
$pl_name[LAST_PLANET + 3] = "House 3";
$pl_name[LAST_PLANET + 4] = "House 4";
$pl_name[LAST_PLANET + 5] = "House 5";
$pl_name[LAST_PLANET + 6] = "House 6";
$pl_name[LAST_PLANET + 7] = "House 7";
$pl_name[LAST_PLANET + 8] = "House 8";
$pl_name[LAST_PLANET + 9] = "House 9";
$pl_name[LAST_PLANET + 10] = "MC (Midheaven)";
$pl_name[LAST_PLANET + 11] = "House 11";
$pl_name[LAST_PLANET + 12] = "House 12";

$asp_name[1] = "Conjunction";
$asp_name[2] = "Opposition";
$asp_name[3] = "Trine";
$asp_name[4] = "Square";
$asp_name[5] = "Sextile";

$asp_glyph[1] = 113;		//  0 deg
$asp_glyph[2] = 119;		//180 deg
$asp_glyph[3] = 101;		//120 deg
$asp_glyph[4] = 114;		// 90 deg
$asp_glyph[5] = 116;		// 60 deg

$exact_aspect_special[0] = 0;			//defines the degrees of the exact aspect
$exact_aspect_special[1] = 60;
$exact_aspect_special[2] = 90;
$exact_aspect_special[3] = 120;
$exact_aspect_special[4] = 180;

$asp_glyph_special[0] = 113;		//  0 deg
$asp_glyph_special[1] = 116;		// 60 deg
$asp_glyph_special[2] = 114;		// 90 deg
$asp_glyph_special[3] = 101;		//120 deg
$asp_glyph_special[4] = 119;		//180 deg

$sign_glyph[1] = 97;
$sign_glyph[2] = 115;
$sign_glyph[3] = 100;
$sign_glyph[4] = 102;
$sign_glyph[5] = 103;
$sign_glyph[6] = 104;
$sign_glyph[7] = 106;
$sign_glyph[8] = 107;
$sign_glyph[9] = 108;
$sign_glyph[10] = 122;
$sign_glyph[11] = 120;
$sign_glyph[12] = 99;


define(CLR_BLACK, "#000000");
define(CLR_WHITE, "#ffffff");

define(CLR_RED, "#ff0000");
define(CLR_ANOTHER_RED, "#ff3c3c");

define(CLR_GREEN, "#2dac00");
define(CLR_LIME, "#9cce04");

define(CLR_BLUE, "#0000ff");
define(CLR_LIGHT_BLUE, "#c0c0ff");
define(CLR_ANOTHER_BLUE, "#c0c0ff");

define(CLR_PURPLE, "#ff00ff");
define(CLR_CYAN, "#00ffff");

define(CLR_YELLOW, "#ffff00");

define(CLR_GRAY, "#c0c0c0");
define(CLR_ANOTHER_GRAY, "#e0e0e0");

define(CLR_ORANGE, "#db9b40");

define(CLR_10TH_H, "#0000ff");
define(CLR_11TH_H, "#ff0000");
define(CLR_12TH_H, "#2dac00");
define(CLR_1ST_H, "#840da9");
define(CLR_2ND_H, "#c0004d");
define(CLR_3RD_H, "#808080");

?>

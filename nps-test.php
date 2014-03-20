/**
 * NPS Test suite
 * If you've found an interesting usecase submit a bug report in this
 * project's repo on Github
 */

$tests_simple=array(
    "This is *bold*. The rest is not",
    "This is in _italics_. The rest is not",
    "This is __underlined__. The rest is not",
    "These are\n\ntwo paragraphs",
    "This is\none paragraph",
    "This is a line with\~a linebreak",
    "Putting *everything* together. This has\n\n 3 __paragraphs__\\~with one
manual break, one ___escaped___ break, yet\n\n\n\n\n---every--- emphasis
element and some <> angular > brackets ][ and random stars. This by the way
should **NOT** be bold. Here's some `code  with \`   weird spacing and an escape
attempt. Boy this is a long one! No <br> linebreak here though`.",
    "   This should be\ttrimmed and     spacing should be fixed."
    );

$results_simple=array(
    "<p>This is <b>bold</b>. The rest is not</p>",
    "<p>This is in <i>italics</i>. The rest is not</p>",
    "<p>This is <u>underlined</u>. The rest is not</p>",
    "<p>This is <u>underlined</u>. The rest is not</p>",
    "<p>These are</p>\n<p>two paragraphs</p>",
    "<p>This is one paragraph</p>"
    );


include_once 'notation.php';

for($i=0; $i<count($tests_simple), $i++){

}
<?php

require '../../inc/load.inc.php';

$title = (!empty($_GET['title']) ? htmlentities($_GET['title']).' — ' : '').'<a href="'.SITE_DIR.LEXEMES_SITE_DIR.'party.php">Lexemes Party</a>';
define('PAGE_TITLE', $title);
page::setMenu('lexemes');

require '../../inc/header.inc.php';

$party = new LexemeParty();
$party->init();

if (!empty($_GET['query'])) {
    $party->fetchConcepts($_GET['query']);
    if (count($party->concepts) >= 1) {
        $party->fetchConceptsMeta();
        $items = $party->queryItems();
        if (count($items) >= 1) {
            $party->computeItems($items);
        }
    }
}

// form
echo '<h2>Query</h2>
<form action="'.SITE_DIR.LEXEMES_SITE_DIR.'party.php" method="get">
<p><label for="title">Title (optional, useful to share your party):</label><br /><input type="text" id="title" name="title" style="width: 40%;" value="'.htmlentities(@$_GET['title']).'" /></p>
<p><label for="query">A SPARQL query that returns a variable named <code>?concept</code> representing Wikidata items:</label><br /><textarea id="query" name="query" style="width: 50%; max-width: 50%;">'.htmlentities(@$_GET['query']).'</textarea></p>
<p><label for="languages_filter">Languages filter (optional, codes from <a href="https://www.wikidata.org/wiki/Property:P424">P424</a>, values separated by a space)</label> to <input type="radio" id="languages_allow" name="languages_filter_action" value="allow" '.(($party->languages_filter_action === 'allow') ? 'checked="checked" ' : '').'/> <label for="languages_allow">allow</label> <input type="radio" id="languages_block" name="languages_filter_action" value="block" '.(($party->languages_filter_action === 'block') ? 'checked="checked" ' : '').'/> <label for="languages_block">block</label>:<br /><input type="text" id="languages_filter" name="languages_filter" style="width: 40%;" value="'.(isset($_GET['languages_filter']) ? htmlentities($_GET['languages_filter']) : '').'" /></p>
<p>Display:
<br /><input type="radio" id="languages_rows" name="languages_direction" value="rows" '.(($party->languages_direction === 'rows') ? 'checked="checked" ' : '').'/> <label for="languages_rows">languages in rows, concepts in columns (best for high number of languages)</label>
<br /><input type="radio" id="languages_columns" name="languages_direction" value="columns" '.(($party->languages_direction === 'columns') ? 'checked="checked" ' : '').'/> <label for="languages_columns">languages in columns, concepts in rows (best for high number of concepts)</label></p>
<p><label for="language_display">Language:</label><br /><input type="text" id="language_display" name="language_display" style="width: 100px;" value="'.htmlentities($party->language_display).'" /></p>
<p><input type="submit" value="Search" /></p>
<p>Examples: <a href="'.SITE_DIR.LEXEMES_SITE_DIR.'party.php?title=Colors+of+the+rainbow+flag&query=SELECT+%3Fconcept+{+wd%3AQ51401+p%3AP462+[+rdf%3Atype+wikibase%3ABestRank+%3B+ps%3AP462+%3Fconcept+%3B+pq%3AP1545+%3Frank+]+}+ORDER+BY+xsd%3Ainteger(%3Frank)&languages_filter_action=block&languages_filter=&languages_direction=rows">colors of the rainbow flag</a>, <a href="'.SITE_DIR.LEXEMES_SITE_DIR.'party.php?title=Planets+of+the+Solar+System&query=SELECT+%3Fconcept+{+VALUES+%3Fconcept+{+wd%3AQ308+wd%3AQ313+wd%3AQ2+wd%3AQ111+wd%3AQ319+wd%3AQ193+wd%3AQ324+wd%3AQ332+}+}&languages_filter_action=block&languages_filter=&languages_direction=rows">planets of the Solar System</a>, <a href="'.SITE_DIR.LEXEMES_SITE_DIR.'party.php?title=Animals&query=SELECT+DISTINCT+%3Fconcept+{+%3Fconcept+wdt%3AP31%3F%2Fwdt%3AP279*+wd%3AQ729+%3B+wikibase%3Asitelinks+%3Fsitelinks+}+ORDER+BY+DESC(%3Fsitelinks)+LIMIT+50&languages_filter_action=allow&languages_filter=de+en+fr&languages_direction=columns">animals</a>, <a href="'.SITE_DIR.LEXEMES_SITE_DIR.'party.php?title=Focus+languages&query=SELECT+%3Fconcept+{+VALUES+%3Fconcept+{+wd%3AQ9610+wd%3AQ36236+wd%3AQ56475+wd%3AQ33578+wd%3AQ32238+wd%3AQ1860+}+}&languages_filter_action=allow&languages_filter=bn+ml+ha+ig+dag+en&languages_direction=columns">focus languages</a>.</p>
</form>';

// display main table
if (!empty($party->items)) {
    $party->display();
}

// errors display
if (!empty($party->errors)) {
    echo '<h2>Errors</h2>
<ul>';
    $errors = array_unique($party->errors);
    sort($errors);
    foreach ($errors as $error) {
        echo '<li>'.$error.'</li>';
    }
    echo '</ul>';
}

require '../../inc/footer.inc.php';

?>
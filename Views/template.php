<?php $title = 'Test du template'; listen(); ?>

hello

<?php fillTemplate('default', ['title' => $title]); ?>
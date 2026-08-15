<?php
$targetFolder = '/home/jutawnas/promtinglibabry/public/uploads';
$linkFolder = '/home/jutawnas/public_html/prompts.jutawan.asia/uploads';

if(symlink($targetFolder, $linkFolder)) {
    echo "Berjaya! Jambatan uploads dah siap.";
} else {
    echo "Gagal. Mungkin folder uploads dah ada, tolong delete dulu.";
}
?>
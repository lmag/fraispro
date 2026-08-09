<?php
/* Copyright (C) 2026 Eoxia
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * \file    view/frontend/drafts.php
 * \ingroup fraispro
 * \brief   PWA App view to list pending scanned receipts (drafts)
 */

// Load Dolibarr environment
$res = 0;
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && file_exists("../../../../main.inc.php")) {
	$res = @include "../../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/custom/fraispro/class/fraispro_receipt.class.php';

global $conf, $db, $langs, $user;

$langs->loadLangs(['fraispro@fraispro', 'projects']);

$title = 'Brouillons Frais.pro';
$help_url = '';

// Include Saturne libs
$moreJS   = [
    '/custom/saturne/js/saturne.min.js',
    '/custom/saturne/js/includes/hammer.min.js'
];
$moreCSS  = [
    '/custom/saturne/css/saturne.min.css'
];

$conf->dol_hide_topmenu  = 1;
$conf->dol_hide_leftmenu = 1;

llxHeader('', $title, $help_url, '', 0, 0, $moreJS, $moreCSS, '', 'template-pwa fraispro-app');

// Include Homogeneous App Top Header
$pwaHeaderCenterHtml = '<span style="font-weight:600;">Traitement</span>';
$fraispro_header = dol_buildpath('/custom/fraispro/view/frontend/fraispro_pwa_header.tpl.php');
if (file_exists($fraispro_header)) {
    require_once $fraispro_header;
}

print '<div class="pwa-container" style="padding: 10px; max-width: 1000px; margin: 0 auto;">';

print '<h2 style="margin-bottom: 20px;"><i class="fa fa-list"></i> Traitement des reçus</h2>';

// TODO: Query the llx_fraispro_receipt table to list the drafts for the current user
$sql = "SELECT rowid, ref, date_creation, status, description, fk_project FROM " . MAIN_DB_PREFIX . "fraispro_receipt WHERE fk_user_creat = " . ((int)$user->id) . " AND status = 1 AND (fk_expensereport IS NULL OR fk_expensereport = 0) ORDER BY date_creation DESC";
$resql = $db->query($sql);

if ($resql) {
    $num = $db->num_rows($resql);
    if ($num > 0) {
        print '<div class="fraispro-drafts-list" style="display: flex; flex-direction: column; gap: 10px;">';
        
        require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';
        
        while ($obj = $db->fetch_object($resql)) {
            $ref = $obj->ref ? $obj->ref : (string)$obj->rowid; // Fallback to rowid if ref is empty
            $dir = $conf->fraispro->dir_output . '/' . dol_sanitizeFileName($ref);
            $thumbUrl = '';
            
            if (dol_is_dir($dir)) {
                $files = dol_dir_list($dir, 'files', 0, '\.(png|jpg|jpeg|gif|webp)$', '', 'date', SORT_DESC);
                if (!empty($files)) {
                    $firstFile = $files[0]['name'];
                    // Use document.php to serve the image safely
                    $thumbUrl = DOL_URL_ROOT . '/document.php?modulepart=fraispro&entity=1&file=' . urlencode(dol_sanitizeFileName($ref) . '/' . $firstFile);
                }
            }
            
            print '<div class="draft-card" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 15px; display: flex; align-items: center; justify-content: space-between;">';
            print '  <div class="draft-left" style="display: flex; align-items: center; gap: 15px;">';
            
            // Thumbnail
            if ($thumbUrl) {
                print '    <img src="' . $thumbUrl . '" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" alt="Reçu">';
            } else {
                print '    <div style="width: 80px; height: 80px; background: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">';
                print '      <i class="fa fa-file-invoice" style="font-size: 24px; color: #94a3b8;"></i>';
                print '    </div>';
            }
            
            $displayRef = empty($obj->ref) ? '(PROV' . $obj->rowid . ')' : $obj->ref;
            print '    <div class="draft-info">';
            print '      <strong style="color: #1e293b; display: block; font-size: 15px;">' . $displayRef . '</strong>';
            if (!empty($obj->description)) {
                print '      <span style="color: #334155; display: block; font-size: 13px; margin: 2px 0;">' . dol_htmlentities($obj->description) . '</span>';
            }
            print '      <span style="color: #64748b; font-size: 12px;">' . dol_print_date($db->jdate($obj->date_creation), 'dayhour') . '</span>';
            print '    </div>';
            print '  </div>'; // end draft-left
            
            print '  <div class="draft-actions">';
            // Add a simple circular button to view/edit (icon only)
            print '    <a href="#" class="button" style="padding: 0 !important; width: 36px !important; height: 36px !important; min-width: 36px !important; border-radius: 50% !important; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; margin: 0;"><i class="fa fa-pen"></i></a>';
            print '  </div>';
            print '</div>';
        }
        print '</div>';
    } else {
        print '<div style="text-align: center; padding: 40px 20px; color: #94a3b8; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">';
        print '  <i class="fa fa-inbox" style="font-size: 40px; margin-bottom: 15px; color: #cbd5e1;"></i><br>';
        print '  Aucun reçu en attente d\'affectation.';
        print '</div>';
    }
    $db->free($resql);
} else {
    dol_print_error($db);
}

print '</div>'; // end pwa-container

// Include Homogeneous App Bottom Nav
$fraispro_bottom_nav = dol_buildpath('/custom/fraispro/view/frontend/fraispro_pwa_bottom_nav.tpl.php');
if (file_exists($fraispro_bottom_nav)) {
    require_once $fraispro_bottom_nav;
}

llxFooter();
$db->close();

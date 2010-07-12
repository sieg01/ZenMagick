<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2010 zenmagick.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<?php
  $selectedLanguageId = $currentLanguage->getId();
?>

<script type="text/javascript">
    var on = 'ui-icon-check';
    var off = 'ui-icon-close';

    function toggle_status(link) {
        var currentStatus = link.className.split('-')[2];
        var pageId = link.id.split('-')[0];
        var property = link.id.split('-')[1];
        var languageId = $('#languageId option:selected')[0].value;
        $.ajax({
            type: "POST",
            url: "<?php echo $admin2->ajax('EZPages_admin', 'setEZPageProperty') ?>",
            data: 'pageId='+pageId+'&languageId='+languageId+'&property='+property+'&value='+('on' == currentStatus ? 'false' : 'true'),
            success: function(msg) { 
              if ('on' == currentStatus) {
                  $('#'+link.id+' span').addClass(off).removeClass(on);
              } else {
                  $('#'+link.id+' span').addClass(on).removeClass(off);
              }
              link.className = 'ezpage-status-'+('on' == currentStatus ? 'off' : 'on');
            },
            error: function(msg) { 
                alert(msg);
            }
        });
    }

</script>

<?php zm_title($this) ?>
<form action="<?php echo $admin2->url() ?>" method="POST">
  <h2><?php _vzm('EZPage Manager') ?> (
          <select id="languageId" name="languageId" onchange="this.form.submit();">
            <?php foreach (ZMLanguages::instance()->getLanguages() as $lang) { ?>
              <?php $selected = $selectedLanguageId == $lang->getId() ? ' selected="selected"' : ''; ?>
              <option value="<?php echo $lang->getId() ?>"<?php echo $selected ?>><?php echo $lang->getName() ?></option>
            <?php } ?>
          </select>
        )
      <a href="<?php echo $admin2->url(null, 'editId=0&languageId='.$selectedLanguageId) ?>">Create new</a>
  </h2>
</form>

<table cellpadding="5" cellspacing="0" width="80%"> 
  <thead>
    <tr>
      <th><?php _vzm("Id") ?></th>
      <th><?php _vzm("Title") ?></th>
      <th><?php _vzm("New Window") ?></th>
      <th><?php _vzm("Secure") ?></th>
      <th><?php _vzm("Header") ?></th>
      <th><?php _vzm("Sidebar") ?></th>
      <th><?php _vzm("Footer") ?></th>
      <th><?php _vzm("Chapter") ?></th>
      <th><?php _vzm("TOC") ?></th>
      <th><?php _vzm("Action") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php $odd = true; foreach (ZMEZPages::instance()->getAllPages($selectedLanguageId) as $ezPage) { $odd = !$odd; ?>
      <tr<?php if ($odd) { echo ' class="odd"'; } ?>>
        <td><?php echo $ezPage->getId() ?></td>
        <td><a href="<?php echo $admin2->url(null, 'editId='.$ezPage->getId().'&languageId='.$selectedLanguageId) ?>"><?php echo $html->encode($ezPage->getTitle()) ?></a><td>
            <a href="#<?php echo $ezPage->getId().'-NewWin' ?>" onclick="toggle_status(this); return false;" id="<?php echo $ezPage->getId() ?>-NewWin" class="ezpage-status-<?php echo ($ezPage->isNewWin() ? 'on' : 'off') ?>"><span class="ui-icon <?php echo ($ezPage->isNewWin() ? 'ui-icon-check' : 'ui-icon-close') ?>"></a>
        </td>
        <td>
            <a href="#<?php echo $ezPage->getId().'-SSL' ?>" onclick="toggle_status(this); return false;" id="<?php echo $ezPage->getId() ?>-SSL" class="ezpage-status-<?php echo ($ezPage->isSSL() ? 'on' : 'off') ?>"><span class="ui-icon <?php echo ($ezPage->isSSL() ? 'ui-icon-check' : 'ui-icon-close') ?>"></a>
        </td>
        <td>
            <a href="#<?php echo $ezPage->getId().'-header' ?>" onclick="toggle_status(this); return false;" id="<?php echo $ezPage->getId() ?>-header" class="ezpage-status-<?php echo ($ezPage->isHeader() ? 'on' : 'off') ?>"><span class="ui-icon <?php echo ($ezPage->isHeader() ? 'ui-icon-check' : 'ui-icon-close') ?>"></a>
            <?php echo $ezPage->getHeaderSort() ?>
        </td>
        <td>
            <a href="#<?php echo $ezPage->getId().'-sidebox' ?>" onclick="toggle_status(this); return false;" id="<?php echo $ezPage->getId() ?>-sidebox" class="ezpage-status-<?php echo ($ezPage->isSidebox() ? 'on' : 'off') ?>"><span class="ui-icon <?php echo ($ezPage->isSidebox() ? 'ui-icon-check' : 'ui-icon-close') ?>"></a>
            <?php echo $ezPage->getSideboxSort() ?>
        </td>
        <td>
            <a href="#<?php echo $ezPage->getId().'-footer' ?>" onclick="toggle_status(this); return false;" id="<?php echo $ezPage->getId() ?>-footer" class="ezpage-status-<?php echo ($ezPage->isFooter() ? 'on' : 'off') ?>"><span class="ui-icon <?php echo ($ezPage->isFooter() ? 'ui-icon-check' : 'ui-icon-close') ?>"></a>
            <?php echo $ezPage->getFooterSort() ?>
        </td>
        <td><?php echo $ezPage->getTocChapter() ?></td>
        <td>
            <a href="#<?php echo $ezPage->getId().'-toc' ?>" onclick="toggle_status(this); return false;" id="<?php echo $ezPage->getId() ?>-toc" class="ezpage-status-<?php echo ($ezPage->isToc() ? 'on' : 'off') ?>"><span class="ui-icon <?php echo ($ezPage->isToc() ? 'ui-icon-check' : 'ui-icon-close') ?>"></a>
            <?php echo $ezPage->getTocSort() ?>
        </td>
        <td>
          <form action="<?php echo $admin2->url() ?>" method="POST">
            <input type="hidden" name="main_page" value="ezpages">
            <input type="hidden" name="languageId" value="<?php echo $selectedLanguageId ?>">
            <input type="hidden" name="deleteId" value="<?php echo $ezPage->getId() ?>">
            <input type="submit" value="Delete">
            <a href="<?php echo $admin2->url(null, 'editId='.$ezPage->getId().'&languageId='.$selectedLanguageId) ?>">Edit</a>
          </form>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>


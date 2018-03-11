<?php
// 2/4/2012 brad@brgr2.com complete overhaul, now using DataTables and TinyMCE, no tables for layout (except DataTables)
$page_action = tep_fill_variable('page_action', 'get');
$pID = tep_fill_variable('pID', 'get');
$submit_value = tep_fill_variable('submit_value', 'post');

$message = tep_fill_variable('message', 'get');
$pages = tep_fill_variable('pages', 'post', array());
$language_id = tep_fill_variable('language_id', 'post', tep_get_default_language());
$language = tep_get_language_code($language_id);

if ($page_action == 'edit_confirm') {

  $name = tep_fill_variable('name', 'post');
  $comment = tep_fill_variable('comment', 'post');
  $title = tep_fill_variable('title', 'post');
  $keywords = tep_fill_variable('keywords', 'post');
  $description = tep_fill_variable('description', 'post');
  $content = tep_fill_variable('content', 'post');
  $page_lock_status = tep_fill_variable('page_lock_status', 'post');
  $page_url = tep_fill_variable('page_url', 'post');
  $page_order = tep_fill_variable('page_order', 'post');
  $page_group_id = tep_fill_variable('page_group_id', 'post');
  //Check if this is an insert or a update.
  if (is_numeric($pID)) {
    $database->query("update " . TABLE_PAGES . " set last_modified = '" . time() . "', page_group_id = '" . $page_group_id . "', page_lock_status = '" . $page_lock_status . "' where page_id = '" . $pID . "' limit 1");
    $database->query("update " . TABLE_PAGES_DESCRIPTION . " set title = '" . $title . "', keywords = '" . $keywords . "', description = '" . $description . "', name = '" . $name . "', comment = '" . $comment . "', page_order = '" . $page_order . "' where page_id = '" . $pID . "' and language_id = '" . $language_id . "' limit 1");
    $page_query = $database->query("select page_url from " . TABLE_PAGES . " where page_id = '" . $pID . "' limit 1");
    $page_result = $database->fetch_array($page_query);

    tep_write_file(DIR_LANGUAGES . $language . '/basic_text/' . $page_result['page_url'], $content);

    $message = 'Page successfully updated.';
    tep_redirect(FILENAME_ADMIN_PAGES . '?message=' . urlencode($message) . '&pID=' . $pID);
  } else {
    $internal_page_url = tep_page_convert_to_internal_name($page_url);
    if (empty($internal_page_url) || tep_is_page($internal_page_url)) {
      $error->add_error('admin_pages', 'That Page Url is not unique.  Please try again.');
      $page_action = 'edit';
    } else {
      $database->query("insert into " . TABLE_PAGES . " (page_url, page_group_id, page_lock_status, date_added, last_modified) values ('" . $internal_page_url . "', '" . $page_group_id . "', '" . $page_lock_status . "', '" . time() . "', '" . time() . "')");
      $pID = $database->insert_id();
      //Now get the languages and make an entry for each one.
      $query = $database->query("select language_id, code from " . TABLE_LANGUAGES . "");
      foreach($database->fetch_array($query) as $result){
        $database->query("insert into " . TABLE_PAGES_DESCRIPTION . " (page_id, title, keywords, description, name, language_id, comment, page_order) values ('" . $pID . "', '" . $title . "', '" . $keywords . "', '" . $description . "', '" . $name . "', '" . $result['language_id'] . "', '" . $comment . "', '" . $page_order . "')");
        tep_write_file(DIR_LANGUAGES . $result['code'] . '/basic_text/' . $internal_page_url, $content);
      }
      //Now make a language file for each one.  This sytem only makes basic pages unless otherwise generated.
      $pID = '';
      $page_action = '';
      $message = 'Page successfully added.';
      tep_redirect(FILENAME_ADMIN_PAGES . '?message=' . urlencode($message));
    }
  }
}

if ($page_action == 'delete_confirm') {
  $query = $database->query("select language_id, code from " . TABLE_LANGUAGES . "");
  foreach($database->fetch_array($query) as $result){
    tep_delete_file(DIR_LANGUAGES . $result['code'] . '/basic_text/' . tep_get_page_url($pID));
  }
  $database->query("delete from " . TABLE_PAGES . " where page_id = '" . $pID . "'");
  $database->query("delete from " . TABLE_PAGES_DESCRIPTION . " where page_id = '" . $pID . "'");
  $message = 'Page successfully deleted.';
  tep_redirect(FILENAME_ADMIN_PAGES . '?message=' . urlencode($message));
}

if (!$page_action || $page_action == "view") {
  echo '<p><a class="btn btn-primary" href="?page_action=edit"><i class="icon-plus">&nbsp;</i> Create</a>';
}

if ($error->get_error_status('admin_pages')) {
  echo "<div class='alert alert-error'>" . $error->get_error_string('admin_pages') . "</div>";
}

if (($page_action != 'edit')) {

  if (!empty($message)) {
    echo "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>{$message}</div>";
  }
  ?>
  <script>
    var jq = jq || [];
    jq.push(function() {
      initTable();
    });
    function initTable() {
      window.otable = $('.web-page-table').dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span5'i><'span7'p>>",
        "sPaginationType": "bootstrap",
        "bStateSave": true,
        "sScrollX": "100%",
        "aoColumns": [
          null,
          null,
          null,
          {"sWidth": "24px"},
          {"sWidth": "24px"},
          {"sWidth": "24px"},
          null,
          {"sWidth": "96px"}
        ]
      });
    }
    function delete_page(pid) {
      if (confirm("Are you sure you want to delete this page?")) {
        window.location.href = '<?php echo FILENAME_ADMIN_PAGES; ?>?pID=' + pid + '&page_action=delete_confirm';
      }
    }
  </script>
  <table class="table table-striped table-condensed table-hover web-page-table">
    <thead>
      <tr>
        <th>Page Name
        <th>Url
        <th>Comment
        <th>Status
        <th>Content Editable
        <th>Associated Emails
        <th>Modified
        <th>
      </tr>
    </thead>
    <tbody>
      <?php
      $uData = array();
      $query = $database->query("select p.page_id, p.page_url, p.page_lock_status, p.last_modified, pd.comment, pd.name, pd.page_order from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd where p.page_id = pd.page_id and pd.language_id = '1' order by pd.name");
      foreach($database->fetch_array($query) as $result){

        echo "
          <tr>
            <td>{$result['name']}
            <td>{$result['page_url']}
            <td>{$result['comment']}
        ";
        ?>
      <td><?php echo (($result['page_lock_status'] == '1') ? '<i class="icon-lock">&nbsp;</i>' : '<i class="icon-unlock">&nbsp;</i>') ?>
      <td><?php echo ((is_file(DIR_LANGUAGES . $language . '/basic_text/' . $result['page_url'])) ? '<i class="icon-ok">&nbsp;</i>' : '<i class="icon-remove">&nbsp;</i>'); ?>
      <td><?php echo ((is_file(DIR_LANGUAGES . $language . '/email_templates/' . $result['page_url'])) ? '<i class="icon-ok">&nbsp;</i>' : '<i class="icon-remove">&nbsp;</i>'); ?>
      <td><?php echo date("n/d/Y", $result['last_modified']); ?>
          <td><a href="<?php echo FILENAME_ADMIN_PAGES . '?pID=' . $result['page_id'] . '&page_action=edit'; ?>"><i class="icon-large icon-edit">&nbsp;</i></a><?php if (tep_page_can_be_deleted($result['page_url'])) { ?> <a href="javascript:;" onclick="delete_page(<?php echo $result['page_id'] ?>)"><i class="icon-large icon-trash">&nbsp;</i></a><?php } ?></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
    <tfoot>
    <th>Page Name
    <th>Url
    <th>Comment
    <th>Page Status
    <th>Content Editable
    <th>Associated Emails
    <th>Last Modified
    <th>
      </tfoot>
  </table>
  <?php
} else {
?>
  <script data-cfasync="false" src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
  <script language="javascript">
  tinymce.init({
      selector: 'textarea.tinymce',
      plugins: 'anchor advlist autolink code hr link image lists charmap paste textcolor spellchecker table',
      toolbar: 'anchor link advlist autolink code hr image lists charmap paste table forecolor backcolor',
      height: 600
  });
  </script>
  <?php
  //Edit
  if (!is_numeric($pID)) {
    ?>
    <form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_PAGES . '?page_action=edit_confirm'; ?>">
      <?php
      $page_result = array('page_lock_status' => tep_fill_variable('page_lock_status', 'post', '0'),
          'page_url' => tep_fill_variable('page_url', 'post'),
          'page_group_id' => tep_fill_variable('page_group_id', 'post'),
          'title' => tep_fill_variable('title', 'post'),
          'comment' => tep_fill_variable('comment', 'post'),
          'keywords' => tep_fill_variable('keywords', 'post'),
          'description' => tep_fill_variable('description', 'post'),
          'page_url' => tep_fill_variable('page_url', 'post'),
          'page_order' => tep_fill_variable('page_order', 'post'),
          'name' => tep_fill_variable('name', 'post'));
      $button_text = 'Insert';
    } else {
      ?>
      <form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_PAGES . '?page_action=edit_confirm&pID=' . $pID; ?>">
        <?php
        $page_query = $database->query("select p.page_lock_status, p.page_url, p.page_group_id, pd.title, pd.keywords, pd.description, pd.name, pd.comment, pd.page_order from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd where p.page_id = '" . $pID . "' and p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' limit 1");
        $page_result = $database->fetch_array($page_query);
        $button_text = 'Update';
      }

      if (!is_numeric($pID)) {
        ?>
        <div class="row-fluid">
          <label for="page_url" class="span3">Page Url<br><em><small class="muted">This is the actual page filename, it cannot be changed and must be unique</small></em></label>
          <input class="span4" type="text" name="page_url" value="<?php echo $page_result['page_url']; ?>" />
        </div>
        <?php
      }
      ?>
      <div class="row-fluid">
        <label for="page_group_id" class="span3">Page Group</label>
        <?php echo tep_draw_page_group_pulldown('page_group_id', $page_result['page_group_id'], array(array('id' => '', 'name' => 'None'))); ?>
      </div>
      <div class="row-fluid">
        <label class="span3">Page Name<br><em><small class="muted">This is the actual page header</small></em></label>
        <input class="span4" type="text" name="name" value="<?php echo $page_result['name']; ?>" />
      </div>
      <div class="row-fluid">
        <label class="span3">Page Comment<br><em><small class="muted">A note for you, not shown anywhere</small></em></label>
        <input class="span4" type="text" name="comment" value="<?php echo $page_result['comment']; ?>" />
      </div>

      <div class="row-fluid">
        <label class="span3">Page Meta Title<br><em><small class="muted">This is the title that shows up in the top of the browser. Also used in search engine ranking</small></em></label>
        <input class="span4" type="text" name="title" value="<?php echo $page_result['title']; ?>" />
      </div>
      <div class="row-fluid">
        <label class="span3">Page Meta Keywords<br><em><small class="muted">These should be words that describe this page, comma separated</small></em></label>
        <input class="span4" type="text" name="keywords" value="<?php echo $page_result['keywords']; ?>" />
      </div>
      <div class="row-fluid">
        <label class="span3">Page Meta Description<br><em><small class="muted">This should be a brief description about the page.</small></em></label>
      </div>
      <textarea class="input-block-level" name="description" style="min-height: 4em;"><?php echo $page_result['description']; ?></textarea>

      <?php
      if (is_file(DIR_LANGUAGES . $language . '/basic_text/' . $page_result['page_url']) || !is_numeric($pID)) {
        if (is_numeric($pID)) {
          $content = file_get_contents(DIR_LANGUAGES . $language . '/basic_text/' . $page_result['page_url']);
          ?>
          <div class="row-fluid">
            <label class="span3">Page Content</label>
          </div>
          <textarea class="input-block-level tinymce" name="content" style="min-height: 12em;"><?php echo htmlentities(stripslashes($content)); ?></textarea>
          <?php
        } else { // Don't think this is used, but just in case :)
          echo "<input type='hidden' name='content' value='' />";
        }
      }
    }

    if ($page_action == 'edit') {

      $open_status = '';
      $lock_status = '';

      if ($page_result['page_lock_status'] == '0') {
        $open_status = ' checked';
      } else {
        $lock_status = ' checked';
      }
      ?>
      <div class="row-fluid">
        <label class="span3">Page Status</label>
        <input type="radio" name="page_lock_status" value="0" <?php echo $open_status; ?> />Open <input type="radio" name="page_lock_status" value="1" <?php echo $lock_status; ?> /> Locked
      </div>
      <div class="row-fluid">
        <label class="span3">Page Order<br><em><small class="muted">Used to change the order in which the page appears in the navigation menus</small></em></label>
        <input class="span4" type="text" name="page_order" value="<?php echo $page_result['page_order']; ?>" />
      </div>
      <div class="row-fluid">
        <label class="span3">View Translation</label>
        <?php echo tep_draw_language_pulldown('language_id', $language_id, ' onchange="this.form.submit();"'); ?>
      </div>

      <?php
      if (is_file(DIR_LANGUAGES . $language . '/email_templates/' . $page_result['page_url'])) {
        ?>
        <td class="pageBoxContent"><a href="<?php echo FILENAME_ADMIN_EMAILS . '?eID=' . $page_result['page_url'] . '&page_action=edit'; ?>">Edit Associated Email Template</a></td>
        <?php
      }
      ?>
      <div class="row-fluid">
        <p style="text-align: center;">
          <input class="btn btn-primary" type="submit" name="submit_value" value="Update">
          <a class="btn" href="<?php echo FILENAME_ADMIN_PAGES . '?pID=' . $pID . '&page_action=view'; ?>">Cancel</a>
      </div>
      <?php
    }
    ?>
  </form>

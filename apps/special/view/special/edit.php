<?php $this->display('special/header');?>
<link href="<?=IMG_URL?>js/lib/colorInput/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.colorInput.js" type="text/javascript"></script>
<form name="special_edit" id="special_edit" method="POST" action="?app=special&controller=special&action=edit">
<input type="hidden" name="modelid" value="<?=$modelid?>"/>
<input type="hidden" name="contentid" value="<?=$contentid?>" />
<? if($status == 6): ?>
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<? endif; ?>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">	
	<tr>
		<th width="135"><span class="c_red">*</span> 栏目：</th>
		<td><?=element::category('catid', 'catid', $catid)?></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 标题：</th>
		<td>
			<?=element::title('title', $title, $color)?>
			<label><input type="checkbox" name="has_subtitle" id="has_subtitle" value="1" <?=($subtitle ? 'checked' : '')?> class="checkbox_style" onclick="show_subtitle()" /> 副题</label>
		</td>
	</tr>
	<tr id="tr_subtitle" style="display:<?=($subtitle ? 'table-row' : 'none')?>">
		<th>副题：</th>
		<td><input type="text" name="subtitle" id="subtitle" value="<?=$subtitle?>" size="100" maxlength="120" /></td>
	</tr>
	<tr>
		<th>摘要：</th>
		<td><textarea name="description" maxLength="255" style="width:440px;height:120px"  class="bdr"><?=$description?></textarea></td>
	</tr>
    <tr>
        <th>Tags：</th>
        <td><?=element::tag('tags', $tags)?></td>
    </tr>
	<tr>
		<th>缩略图：</th>
		<td><?php echo element::image('thumb',$thumb,45,false);?></td>
	</tr>
	<tr>
		<th>网址：</th>
		<td><?=$path?></td>
	</tr>
    <tr>
        <th><?=element::tips('专题的内容模块可以有更多链接，链接向独立的页面展示该模块的更多内容列表，每个专题可以独立设置一个模板文件')?> 列表页模板：</th>
		<td><?=element::template('morelist_template', 'morelist_template', $morelist_template, 50)?></td>
    </tr>
    <tr>
        <th><?=element::tips('更多内容列表每页显示多少条信息')?> 列表页每页信息数：</th>
        <td><input type="text" name="morelist_pagesize" size="8" value="<?php echo $morelist_pagesize; ?>" /></td>
    </tr>
    <tr>
        <th><?=element::tips('更多内容列表最多展示多少页内容，0 为不限制；超出限制页的内容将不可见')?> 列表最多显示：</th>
        <td><input type="text" name="morelist_maxpage" size="8" value="<?php echo $morelist_maxpage; ?>" /> 页</td>
    </tr>
	<tr>
		<th>属性：</th>
		<td><?=element::property("proid", "proids", $proids)?></td>
	</tr>
	<tr>
		<th><?=element::tips('权重将决定专题在哪里显示和排序')?> 权重：</th>
		<td>
			<?=element::weight($weight, $myweight);?>
		</td>
	</tr>
	<tr>
		<th><?=element::tips('自动更新截止日期，之后将不会自动更新此专题下的页面')?> 更新截止日期：</th>
		<td><input type="text" name="lastupdated" class="input_calendar" value="<?=$lastupdated?>" size="20"/></td>
	</tr>
</table>

<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<tr>
		<th width="135"><?=element::tips('文章定时发布时间')?> 上线：</th>
		<input type="hidden" name="oldpublished" value="<?=$published?>" />
		<td width="170"><input type="text" name="published" class="input_calendar" value="<?=$published?>" size="20"/></td>
		<th width="80">下线：</th>
		<td><input type="text" name="unpublished" class="input_calendar" value="<?=$unpublished?>" size="20"/></td>
	</tr>
	<?php if(priv::aca('system', 'related')): ?>
	<tr>
		<th class="vtop">相关：</th>
		<td colspan="3"><?=element::related($contentid)?></td>
	</tr>
	<?php endif;?>
    <tr>
        <th>评论：</th>
        <td colspan="3"><label><input type="checkbox" name="allowcomment" value="1" <?php if ($allowcomment) echo 'checked';?> class="checkbox_style"/> 允许</label></td>
    </tr>
    <tr>
        <th>状态：</th>
        <td colspan="3"><?=table('status', $status, 'name')?></td>
    </tr>
</table>

<div id="field" onclick="field.expand(this.id)" class="mar_l_8 hand title" title="点击展开" style="display:none;"><span class="span_close">扩展字段</span></div>
<table id="field_c" width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
</table>

<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
    <tr>
        <th width="135">&nbsp;</th>
        <td>
         	<input type="submit" value="保存" class="button_style_2"/>
		</td>
	</tr>
</table>
</form>

<?php $this->display('content/success', 'system');?>

<script type="text/javascript" src="apps/system/js/content.js"></script>
<script type="text/javascript" src="apps/system/js/psn.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<script type="text/javascript" src="apps/system/js/field.js" ></script>

<script type="text/javascript">
var get_tag = <?php echo $get_tag;?> + 0;

// 获取自定义字段
$(function() {
    var catid = $('#catid');
    function categoryReady() {
        catid.data('selectree') && catid.data('selectree').bind('checked', function(item) {
            item.catid && field.getbycid(<?=$contentid?>, item.catid);
            item.catid && content.isModelEnabled(item.catid);
        });
    }
    if (catid.data('selectree')) {
        categoryReady();
    } else {
        catid.bind('categoryReady', categoryReady);
    }
    if(catid.val()) field.getbycid(<?=$contentid?>, catid.val());
});
</script>
</body>
</html>
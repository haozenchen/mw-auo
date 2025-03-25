<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="layout" style="width: 100%;"></div>
<style>
td[col="1"] div.w2ui-col-header,
td[col="2"] div.w2ui-col-header,
td[col="3"] div.w2ui-col-header,
td[col="4"] div.w2ui-col-header,
td[col="5"] div.w2ui-col-header{
        cursor: pointer;
}
td[col="1"] div.w2ui-col-sorted,
td[col="2"] div.w2ui-col-sorted,
td[col="3"] div.w2ui-col-sorted,
td[col="4"] div.w2ui-col-sorted,
td[col="5"] div.w2ui-col-sorted {
		cursor: pointer;
		background-color: #ffecc1 !important;
}
</style>
<script type="text/javascript">
	var wh = jQuery(window).height() - 70;
	jQuery('#layout').height(wh);
	var whc = wh - 15;

    jQuery(function () {

        var currentId = 0;
		var gheight;
		//set layout
		var tlstyle = 'border: 0px; padding: 5px; background-color:#fff';
		var bltyle = 'background-color: #eef4f9; border: 0px; padding: 10px; margin:0px 5px';
		var pstyle = 'border: 1px solid #dfdfdf; padding: 5px;';

        jQuery('#layout').w2layout({
            name: 'layout',
            panels: [
					{ type: 'top', resizable: false, size: wh, style: tlstyle, content: 'top' },
					{ type: 'main', resizable: false, hidden: true, style: bltyle, content: 'main',
						tabs: {
							name: 'tabs',
							active: 'tab1',
							style:'margin:0px 5px',
							tabs: [
								{ id: 'tab1', caption: 'tab1' }
							],
						}
					}
				]
        })
        w2ui['layout'].content('top', '<div id="grid" style="width: 100%; height: ' + whc + 'px;"></div>');
        jQuery('#grid').w2grid({
            name: 'grid',
			recordHeight : 30,
			show: {
				toolbar: true,
				footer: true,
				selectColumn: true,
				toolbar: true,
				toolbarAdd: false,
				toolbarDelete: false,
				toolbarEdit: false
			},
            toolbar: {
				items: [
					{ type: 'spacer' },
					<?php if(!empty($advancePermission['add'])|| $advancePermission =='grant_all'){ ?>
						{ type: 'button', id: 'add', icon: 'fas fa-plus', caption: '新增', tooltip: '新增' },
					<?php } ?>
					<?php if(!empty($advancePermission['delete'])|| $advancePermission =='grant_all'){ ?>
                    	{ type: 'button', id: 'delete', icon: 'fas fa-trash-alt', caption: '刪除', tooltip: '刪除' },
					<?php } ?>

				],
				onClick: function (target, data) {
					if(target == 'add'){
                        w2ui['layout'].load('main','add',);
                        w2ui['layout'].hide('top');
                        w2ui['layout'].show('main');
                        jQuery('#back_btn').show();
                        w2ui['layout']['panels'][1]['tabs']['tabs'][0]['text']="新增帳號";
                    }else if(target == 'delete'){
                    	var selected = w2ui['grid'].getSelection()
						w2confirm('確定刪除此帳號')
                        .yes(function () {
                            jQuery.ajax({
								url: 'delete/'+selected,
								type: 'POST',
								headers: {
							        'X-CSRF-Token': jQuery('meta[name="csrfToken"]').attr('content')
							    },
								dataType: 'json',
								success: function(data) {
									//called when successful
					                if(data.status == 'ok'){
										toastr.success('成功刪除');
										w2ui['grid'].reload();
									}
					                else{
					                    w2alert(data.status);
					                }
								},

							});
                        })
                        .no(function () {

                        });
					}
				}
			},
			multiSearch: false,
			searches: [
				{ field: 'name', caption: '使用者/帳號', type: 'text', operator: 'contains' },

			],
            onDblClick: function(event) {
				openMainLayout();
			},
            url: 'listing',
			limit: 30,
			method: 'GET', // need this to avoid 412 error on Safari
            columns: [
				{ field: 'recid', caption: '<?php echo __('ID', true) ?>', size: '5%', sortable: false },
				{ field: 'name', caption: '<?php echo __('使用者', true) ?>', size: '10%', style: 'text-align: left', sortable: true},
				{ field: 'username', caption: '<?php echo __('帳號', true) ?>', size: '10%', style: 'text-align: left', sortable: true},
				{ field: 'auth_groups', caption: '<?php echo __('權限群組', true) ?>', size: '10%', style: 'text-align: left', sortable: false},
				{ field: 'last_visit', caption: '<?php echo __('最後登入時間', true) ?>', size: '15%', style: 'text-align: left', sortable: true},
				{ field: 'last_visit_from', caption: '<?php echo __('最後登入IP位址', true) ?>', size: '15%', sortable: true},
				{ field: 'active', caption: '<?php echo __('狀態', true) ?>', size: '5%', sortable: true},
				{ field: 'Mfa', caption: '<?php echo __('MFA', true) ?>', size: '5%', sortable: true}
			],
            onLoad: function(event) {
				var response = JSON.parse(event.xhr.responseText);
				setTimeout(() => {
					if(w2ui['grid'].getSelection().length >= 1){
						w2ui['grid_toolbar'].enable('delete');
					}else{
						w2ui['grid_toolbar'].disable('delete');
					}
				}, 700);
			}
        });
        w2ui['grid'].hideColumn('recid', 'accid');
        w2ui.grid.on('click', function(event) {
            currentId = event.recid;
			recs = w2ui['grid'].get(currentId);
            event.onComplete = function () {
                if(w2ui['grid'].getSelection().length >= 1){
					w2ui['grid_toolbar'].enable('delete');
				}else{
					w2ui['grid_toolbar'].disable('delete');
				}
			}
        })
        function openMainLayout(){
            w2ui['layout'].load('main', "<?php echo $this->Url->build(['controller' => 'SaasAdmins', 'action' => 'edit']); ?>/" + currentId);
			w2ui['layout'].hide('top');
			w2ui['layout'].show('main');
			w2ui['layout_main_tabs'].active = 'tab1';
            w2ui['layout']['panels'][1]['tabs']['tabs'][0]['text']="修改帳號";
			jQuery('#switch_btn').hide();
			jQuery('#back_btn').show();
		}
    })
</script>
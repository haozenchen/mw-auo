<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="layout" style="width: 100%;"></div>
<style>
td div.w2ui-col-header{
        cursor: pointer;
}
td div.w2ui-col-sorted {
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
        var child_grid = false;
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
	            toolbarDelete: false,
	            toolbarSave: false,
	            toolbarEdit: false,
	            toolbarSearch: false,
	            toolbarInput: false,
	        },
	        toolbar:{
	        	items: [
	        		<?php if(!empty($advancePermission['add'])|| $advancePermission =='grant_all'){ ?>
		            { type: 'button', id: 'add', text: '手動執行同步作業', icon: 'w2ui-icon-plus' },
		        	<?php }?>
		        ],
	        	onClick: function (target, data) {
					if(target == 'add'){
                        add();
					}
				}
	        },
            url: 'listing',
			limit: 30,
			method: 'GET', // need this to avoid 412 error on Safari
            columns: [
				{ field: 'recid', caption: '<?php echo __('ID', true) ?>', size: '5%', sortable: false },
				{ field: 'type', caption: '<?php echo __('執行類型', true) ?>', size: '8%', sortable: true},
				{ field: 'status', caption: '<?php echo __('執行狀態', true) ?>', size: '5%', sortable: true},
				{ field: 'user_total', caption: '<?php echo __('AUO員工', true) ?>', size: '5%', sortable: true},
				{ field: 'department_total', caption: '<?php echo __('AUO部門', true) ?>', size: '5%', sortable: true},
				{ field: 'user_update', caption: '<?php echo __('員工異動', true) ?>', size: '5%', sortable: true},
				{ field: 'department_update', caption: '<?php echo __('部門異動', true) ?>', size: '5%', sortable: true},
				{ field: 'username', caption: '<?php echo __('操作者', true) ?>', size: '8%', style: 'text-align: left', sortable: true},
				{ field: 'ip_address_ip', caption: '<?php echo __('ip位址', true) ?>', size: '8%', style: 'text-align: left', sortable: true},
				{ field: 'created', caption: '<?php echo __('時間', true) ?>', size: '8%', style: 'text-align: left', sortable: true},
			],
            onLoad: function(event) {
				var response = JSON.parse(event.xhr.responseText);
			},
			onDblClick: function(event) {
				openMainLayout();
			},
        });
        w2ui['grid'].hideColumn('recid');
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
            w2ui['layout'].load('main', "<?php echo $this->Url->build(['controller' => 'SyncRecords', 'action' => 'log_listing']); ?>/" + currentId);
            if(child_grid){
                w2ui['child_grid'].destroy();
            }
			w2ui['layout'].hide('top');
			w2ui['layout'].show('main');
			w2ui['layout_main_tabs'].active = 'tab1';
			jQuery('#switch_btn').hide();
			jQuery('#back_btn').show();
			child_grid = true;
		}


		
		function add(){
			w2utils.lock('body', '同步中，請稍後...', true);
	        jQuery.ajax({
				url: '<?php echo $this->Url->build(['controller' => 'SyncRecords', 'action' => 'do_sync']); ?>',
				type: 'POST',
				success: function(data) {
					w2utils.unlock('body');
					var res = JSON.parse(data);
	                if(res.status == 'ok'){
	                	toastr.success('同步結束');
	                    w2ui['grid'].reload();
					}
	                else{
	                    w2alert(data.status);
	                }
				},

			});
	    }
    })
</script>
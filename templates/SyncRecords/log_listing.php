<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="child_grid" style="width: 100%;"></div>

<script type="text/javascript">
	var wh = jQuery(window).height() - 70;
	var whc = wh - 15;

    jQuery(function () {
        var currentId = 0;
        var child_currentId = 0;
		var gheight;
		//set layout
		var tlstyle = 'border: 0px; padding: 5px; background-color:#fff';
		var bltyle = 'background-color: #eef4f9; border: 0px; padding: 10px; margin:0px 5px';
		var pstyle = 'border: 1px solid #dfdfdf; padding: 5px;';

        jQuery('#child_grid').height(whc-60);
        jQuery('#child_grid').w2grid({
            name: 'child_grid',
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
					{ type: 'check', caption: '員工', count: '<?php echo $count['user_total']?>' },
                    { type: 'check', caption: '部門', count: '<?php echo $count['department_total']?>' },
                    { type: 'break' },
                    { type: 'check', caption: '員工異動', count: '<?php echo $count['user_update']?>' },
                    { type: 'check', caption: '部門異動', count: '<?php echo $count['department_update']?>' },
                    { type: 'spacer' },
                    { type: 'check', caption: '員工閾值', count: '<?php echo $count['user_threshold']?>' },
                    { type: 'check', caption: '部門閾值', count: '<?php echo $count['department_threshold']?>' },
				]
			},
            multiSearch: false,
            search:{hidden: true},
            onDblClick: function(event) {
				openMainLayout();
			},
            url: 'synclog/'+'<?php echo $record_id?>',
			limit: 30,
			method: 'GET', // need this to avoid 412 error on Safari
            columns: [
				{ field: 'recid', caption: '<?php echo __('ID', true) ?>', size: '5%', sortable: false },
				{ field: 'type', caption: '<?php echo __('執行類型', true) ?>', size: '5%', style: 'text-align: left', sortable: true},
				{ field: 'api_host', caption: '<?php echo __('API類別', true) ?>', size: '5%', style: 'text-align: left', sortable: true},
				{ field: 'action', caption: '<?php echo __('執行動作', true) ?>', size: '8%', style: 'text-align: left', sortable: true},
				{ field: 'total', caption: '<?php echo __('總筆數', true) ?>', size: '3%', style: 'text-align: left', sortable: true},
				{ field: 'success', caption: '<?php echo __('成功筆數', true) ?>', size: '3%', style: 'text-align: left', sortable: true},
				{ field: 'error', caption: '<?php echo __('失敗筆數', true) ?>', size: '3%', style: 'text-align: left', sortable: true},
				{ field: 'status', caption: '<?php echo __('狀態', true) ?>', size: '5%', style: 'text-align: left', sortable: true},
				{ field: 'created', caption: '<?php echo __('執行時間', true) ?>', size: '5%', style: 'text-align: left', sortable: true}
			],
            onLoad: function(response) {
				w2ui['grid_toolbar'].disable('delete');
			},
        });
        w2ui['child_grid'].hideColumn('recid', 'accid');
        w2ui['child_grid_toolbar'].hide('w2ui-search');w2ui['child_grid_toolbar'].disable('delete');
        w2ui['child_grid'].on('click', function(event) {
            currentId = event.recid;
			recs = w2ui['child_grid'].get(currentId);
            event.onComplete = function () {

                if(w2ui['child_grid'].getSelection().length >= 1){
					w2ui['child_grid_toolbar'].enable('delete');

				}else{
					w2ui['child_grid_toolbar'].disable('delete');
				}
			}
        });

        w2ui['layout']['panels'][1]['tabs']['tabs'][0]['text']= "<?php echo $created?> 同步紀錄";
        function openMainLayout(){
			w2popup.load({width: 750, height: 400, showMax:true, title: '同步紀錄 Messenge ', url: "<?php echo $this->Url->build(['controller' => 'SyncRecords', 'action' => 'show_msg']); ?>/"+ currentId});
		}
    })
</script>
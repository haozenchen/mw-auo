<div id="add" class="content" style="display:none;"></div>
<div id="edit" class="content" style="display:none;"></div>
<?php require('listing.php'); ?>

<script type="text/javascript">
    var wh = jQuery(window).height() - 70;
	jQuery('#layout').height(wh);
    //set layout
    var bltyle = 'background-color: #eef4f9; border: 0px; padding: 10px; margin:0px 5px;';

    jQuery(function () {
        jQuery('#layout').w2layout({
            name: 'layout',
            panels: [
                { type: 'main', resizable: false, hidden: true, style: bltyle, content: 'main',
                    tabs: {
                        name: 'tabs',
                        active: 'tab1',
                        style:'margin:0px 5px',
                        tabs: [
                            { id: 'tab1', caption: '選單/功能' },
                        ],
                        onClick: function (event) {
                            switch(event.target){
                                case 'tab1':
                                    goBack();
                                    w2ui['grid'].destroy();
                                    loadList();
                                    break;
                            }
                        }
                    }
                }
            ]
        });
        loadList();
    });
</script>





cms.createPlugin('inputContent',function(elem){
    
    if($('#modal-contents').is('#modal-contents')){
       $('#modal-contents').mazeDialog('open');
       return false;
    }
    
    var $el = $(elem);
    
    $('<div>', {id:'modal-contents'}).append($('#content-filter-modal').show()).append($('<div>', {id:'modal-contents-grid'})).mazeDialog({
                title: 'Материалы',
                draggable: false,
                resize: false,
                height: 'auto',
                width: 800,
                modal: true,
                mode: "fixed",
                open:function(){
                    $("#modal-contents-grid").resize();
                   
                },
                toolbarhead: {minidialog: null, fulldialog: null},              
    });
    
  $('#content-filter-modal').wrap('<form class="filter-content"></form>')
    $('#content-filter-modal').on('change', function(){
        var param = $('.filter-content').serializeObject();
        param.csrf = $('meta[name=csrf-token]').attr('content');
        $("#modal-contents-grid").mazeGrid("update",param,true);
    })
   
   

    $("#modal-contents-grid").mazeGrid({
      colModel:[
          {"name":"title","title":"\u0417\u0430\u0433\u043e\u043b\u043e\u0432\u043e\u043a \u043c\u0430\u0442\u0435\u0440\u0438\u0430\u043b\u0430","width":250,"align":"left","hidefild":true,"sorttable":true},
          {"name":"alias","title":"\u0410\u043b\u0438\u0430\u0441","index":"route.alias","width":150,"align":"center","hidefild":true,"sorttable":true},
          {"name":"typename","title":"\u0422\u0438\u043f\u044b \u043c\u0430\u0442\u0435\u0440\u0438\u0430\u043b\u043e\u0432","index":"c.bundle","width":100,"align":"center","hidefild":true,"sorttable":true},
          {"name":"langtitle","title":"\u042f\u0437\u044b\u043a","index":"l.id_lang","width":150,"align":"center","hidefild":true,"sorttable":true},
          {"name":"roletitle","title":"\u0423\u0440\u043e\u0432\u0435\u043d\u044c \u0434\u043e\u0441\u0442\u0443\u043f\u0430","index":"r.id_role","width":150,"align":"center","hidefild":true,"sorttable":true},
          {"name":"contents_id","title":"ID","index":"c.contents_id","width":20,"align":"center","hidefild":true,"sorttable":true}
        ],
    "plugins":"checkbox,core,tree,movesort,contextmenu,tooltip,buttonfild,edits,tooltip_content",
    "url":"/admin/contents?run=modal&clear=ajax",
    "page":1,
    "datatype":"server",
    "pagename":"number",
    "ordername":"order",
    "fildname":"colonum",
    "rowname":"pnumber",
    "showBottomPanel":true,
    "sortfild":"c.bundle, c.sort",
    "sortorder":"ASC",
    "filterData":function(data){return data.html},
    "colHide":[],
    "params":{"csrf":$('meta[name=csrf-token]').attr('content')},
    "grouping":true,
    "opengroup":true,
    "textpreloader":"\u0417\u0430\u0433\u0440\u0443\u0437\u043a\u0430...",
    "labelmenu":{"textasc":"\u041f\u043e \u0432\u043e\u0437\u0440\u0430\u0441\u0442\u0430\u043d\u0438\u044e",
    "textdesc":"\u041f\u043e \u0443\u0431\u044b\u0432\u0430\u043d\u0438\u044e",
    "textgroup":"\u0413\u0440\u0443\u043f\u043f\u0438\u0440\u043e\u0432\u0430\u0442\u044c"},
    "groupingView":{"groupName":function(arr, fild, titleFild){var title = 'Группировка ';  $.each(titleFild, function(i, val){ title += ' '+val+' - '+arr[i] }); return title}},"sortCol":[],"rowNum":10,"rowList":[3,5,10,20,30,40,50,60,80,70],"minWidth":600,"height":"auto","overlay":true,"labelinfo":"\u041f\u043e\u043a\u0430\u0437\u0430\u043d\u043e {FIRST} - {LAST} \u0438\u0437 {SIZE}","labelrow":"\u0417\u0430\u043f\u0438\u0441\u0435\u0439","labelpage":"\u0438\u0437 {SIZE}","mode":"default",   
      
    });
    
    $("#modal-contents-grid").on('afterGetContent.mazegrid', function(e, obj){
         $('#modal-contents').mazeDialog('update')
        $(this).find('tbody tr').click(function(){
            var trTarget = $(this);
            var data = $(this).data('gridRow');
            var parent = $el.closest('.input-group')
            parent.find('input[type=text]').val(data.title);
            parent.find('input[type=hidden]').val(data.contents_id);
            $('#modal-contents').mazeDialog('close');
        })
        
    })
    
})

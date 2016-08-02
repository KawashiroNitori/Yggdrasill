$(function(){
    var getTimeDiff=function(thentime, nowtime){
        if (nowtime==undefined)
            nowtime=parseInt(moment().format('X'));
        var ms = moment.unix(nowtime).diff(moment.unix(thentime));
        var d = moment.duration(ms);
        return Math.floor(d.asHours()) + moment.utc(ms).format(":mm:ss");
    };
    
    
    moment.locale('zh_cn');
        
    $('.group-btn').click(function(e){
        var target=$(e.target);
        while (target.attr('group-id')==undefined)
            target=target.parent();
        var group_id=target.attr('group-id');
        var dest_url='/group/get_episode/'+group_id;
        $('.episode-list').hide();
        $('.no-select-tip').hide();
        $('.now-running').hide();
        $('.now-paused').hide();
        $('.loading-mask').show();
        
        $.ajax({
            url:dest_url,
            type:'GET',
            dataType:'JSON',
            success: function(data){
                if (data.errcode==10001)
                {
                    location.reload();
                    return;
                }
                else if (data.errcode!=0)
                {
                    $('.no-select-tip').text('发生错误：'+data.errmsg);
                    $('loading-mask').hide();
                    $('no-select-tip').show();
                }
                var list=$('.episode-table>tbody'),i=0,str='',timediff='';
                var nowStatus=data.info[0].status;
                list.empty();

                if (nowStatus!=target.attr('status'))
                {
                    target.attr('status',nowStatus);
                    str='<span class="'+nowStatus+'-light"></span>';
                    if (nowStatus=='running')
                        str+='进行中';
                    else if (nowStatus=='paused')
                        str+='已暂停';
                    else if (nowStatus=='ended')
                        str+='已结束';
                    target.find('.group-status').html(str);
                    str='';
                }
                
                if (nowStatus!='ended')
                {
                    if (nowStatus=='running')
                    {
                        $('.now-running').html('<div class="flex-container"><div class="flex-item"><a target="_blank" href="/episode/view/'
                        +group_id+'/'+data.info[0].access_code+
                        '"><strong>当前章节正在进行中。</strong>点击进入</a></div><div class="raw-item">当前用时：<span class="realtime-box" start-time="'
                        +data.info[0].start_time+
                        '">'
                        +getTimeDiff(data.info[0].start_time)+
                        '</span></div></div>');
                        $('.now-running').show();
                    }
                    else if (nowStatus=='paused')
                    {
                        $('.now-paused').html('<a target="_blank" href="/episode/view/'
                        +group_id+'/'+data.info[0].access_code+
                        '"><strong>当前章节已暂停。</strong>点击进入</a>');
                        $('.now-paused').show();
                    }
                }
                
                for (var ep in data.info)
                {
                    ep=parseInt(ep);
                    if (data.info[ep].status=='ended')
                    {
                        str='';
                        i++;
                        
                        str+='<tr>';
                        str+='<th class="text-right" scope="row">'+i+'</th>';
                        str+='<td><a target="_blank" href="/episode/view/'+group_id+'/'+data.info[ep].access_code+'">'+data.info[ep].name+'</a></td>';
                        str+='<td class="text-right" data-toggle="tooltip" data-placement="left" data-container="body" title="'+moment.unix(data.info[ep].start_time).format('YYYY-MM-DD HH:mm:ss')+'">'+moment.unix(data.info[ep].start_time).fromNow()+'</td>';
                        str+='<td>'+getTimeDiff(data.info[ep].start_time, data.info[ep].end_time)+'</td>';
                        str+='<td><a class="btn btn-primary btn-xs" role="button" href="/episode/download/'+group_id+'/'+data.info[ep].access_code+'"><span class="glyphicon glyphicon-cloud-download"></span>下载</a></td>';
                        str+='<tr>';
                        list.append(str);
                    }
                }
                
                $('[data-toggle="tooltip"]').tooltip();
                $('.loading-mask').hide();
                if (data.info[0].status=='ended' || data.info.length>1)
                    $('.episode-list').show();
            },
            error: function(jqXHR){
                $('.no-select-tip').text('发生错误，请稍后重试。');
                $('.loading-mask').hide();
                $('.no-select-tip').show();
            }
        });
    });
    
    setInterval(function(){
        $('.realtime-box').each(function(){
            $(this).text(getTimeDiff($(this).attr('start-time')));
        });
    },1000);
});

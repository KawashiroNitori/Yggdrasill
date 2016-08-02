$(function(){
    var lastUUID=$('.chat-thread dt:first-child').attr('uuid');
    var isFocus;
    var unread=0;
    var title=$('title').text();
    if (lastUUID==undefined)
        lastUUID='';
    
    window.onfocus=function(){
        isFocus=true;
        unread=0;
        $('title').text(title);
    };
    
    window.onblur=function(){
        isFocus=false;
    };
    
    
    
    var longPoll=function(){
        var dest_url='/group/get_chat/'+$('.chat-thread').attr('group-id')+'/'+lastUUID;
        $.ajax({
            url: dest_url,
            type: 'GET',
            dataType: 'JSON',
            timeout: 30000,
            success: function(data){
                var thread=$('.chat-thread');
                if (data.errcode==0)
                {
                    for (var i in data.content)
                    {
                        i=parseInt(i);
                        var chatRow=data.content[i];
                        if (chatRow.type=='chat' || chatRow.type=='dice')
                        {
                            if (chatRow.type=='dice')
                            {
                                chatRow.chat_text='【投骰】 '+chatRow.chat_text+' = '+chatRow.dice_result;
                            }
                            thread.prepend('<dt uuid="'+chatRow.uuid+'">'+chatRow.name+'</dt><dd>'+chatRow.chat_text+'</dd>');
                        }
                    }
                    var last=data.content[data.content.length-1];
                    lastUUID=last.uuid;
                    if (!isFocus)
                    {
                        unread+=data.content.length;
                        $('title').text('（'+unread+'条未读）'+title);
                    }
                    if (last.type=='end' || last.type=='pause')
                    {
                        location.reload();
                    }
                }
            },
            complete: function(){
                longPoll();
            }
        });
    };
    
    if ($('.chat-thread').attr('status')=='running')
        longPoll();
});
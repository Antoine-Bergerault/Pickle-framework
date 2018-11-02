</div>

<?php loadJS('libraries/jQuery'); ?>

<?php if( $GLOBALS['data'] ): ?>

<noscript>
<h4 style="text-align:center">You must active javascript for this webpage</h4>
</noscript>

<script>

$(function(){

    function check(element, from = false, k = 'key'){
        let key = $(element).data(k);//get the key
        let r = typeof key != 'undefined' && (from == false || key == from);
        if(r == true){
            let i = $(element).data('if');
            if(typeof i !== 'undefined'){
                if(typeof eval(json[i]) !== 'undefined'){
                    if(!eval(json[i])){
                        r = false;
                    }
                }else if(typeof eval(i) !== 'undefined'){ 
                    if(!eval(i)){
                        r = false;
                    }
                }
            }
        }
        return r?key:false;
    }

    function uncheck(element){
        $(element).removeAttr('data-key');
    }

    function update(element, val, arr = []){
        if(typeof val == 'undefined') val = 'undefined';
        if(val == null) val = 'null';
        if(Array.isArray(val)) val = JSON.stringify(val);
        if(val === true) val = 'true';
        if(val === false) val = 'false';
        let t = check(element,false,'transform');
        if(t != false){
            val = window[t](val);
            arr[arr.length] = 'transform';
        }
        $(element).html(val);
        clearData(element);
        for(let a = 0;a<arr.length;a++){
            $(element).removeAttr('data-'+arr[a]);
        }
    }

    function clearData(element){
        $(element).removeAttr('data-foreach');
        $(element).removeAttr('data-foreach-limit');

        $(element).removeAttr('data-key');
        $(element).removeAttr('data-global-key');
        
        $(element).removeAttr('data-explore');
        $(element).removeAttr('data-if')

        if($(element).hasClass('data')){
            $(element).removeClass('data');
            $(element).addClass('data-loaded');
        }
    }

    function load(element,arr,children = false){
        if(children){
            if(check(element) != false){
                load(element,arr);
            }else if(check(element,false,'global-key')){
                load(element,json[check(element,false,'global-key')]);
            }else{
                $(element).children().each(function(i,e){
                    load(e,arr,true);
                });
            }
        }else{

            let f;
            if(f = check(element,false,'foreach')){//if there is a foreach value
                let children = $(element).children().clone();//clone the children
                $(element).html("");//clear the element
                let n = 0,i = 1,len = arr.length;
                let limit = check(element,false,'foreach-limit');
                if(limit != false){
                    limit = limit+'';
                    limit = limit.split(':');
                    if(limit.length == 1){
                        len = (arr.length<limit[0])?arr.length:limit[0];
                    }else if(limit.length >= 2){
                        if(limit[0]<limit[1]){
                            n = limit[0];
                        }else{
                            throw 'foreach-limit passed value not working'
                        }
                        len = (arr.length<limit[1])?arr.length:limit[1];
                        if(limit.length >= 3){
                            i = limit[2];
                        }
                    }
                    $(element).removeAttr('data-foreach-limit');
                }
                for(let a = parseInt(n);a<parseInt(len);a += parseInt(i)){//foreach value append the children
                    children.each(function(index,element){
                        if(check(element,f)){
                            load(element,arr[a]);
                        }
                        if(!check(element)){
                            //console.log($(element));
                            load(element,arr[a],true);
                        }
                    });
                    $(element).append(children.clone());//append new children
                }
            }else if(f = check(element,false,'explore')){//if there is an explore value
                if(check(element)){
                    $(element).children().each(function(index,e){
                        load(e,arr);
                    });
                }else{
                    $(element).children().each(function(index,e){
                        load(e,arr[f]);
                    });
                }
            }else{//else
                let p = $(element).data('property');
                if(p = check(element,false,'property')){
                    update(element,arr[p],['property']);
                }else{
                    update(element,arr);
                }
            }

            if(check(element, false, 'onload')){
                $(element).html($(element).data('onload'));
                $(element).removeAttr('data-onload');
            }

        }

        clearData(element);
        return;

    }

    let json;
    let page = $('#page').clone().html();

    function request(){
        $.get("<?=url(\Pickle\Engine\Router::$ajaxurl)?>",function(data){
            json = JSON.parse(data);//parse data
            render();
        })
    }

    request()

    <?php if( $GLOBALS['refresh'] != false): ?>

    let i = setInterval(request,<?=$GLOBALS['refresh']?>);

    <?php endif ?>
    function render(){
        $('#page').html(page);
        $('.data').each(function(index,element){
            let k;
            if(k = check(element)){
                load(element,json[k]);
            }else if(k = check(element,false,'global-key')){
                load(element,json[k]);
            }
        });
        $('[data-onload]').each(function(index, element){
            let action = check(element, false, 'onload');
            if(action != false){
                window[action](element);
            }
        });
    }

});

</script>

<?php endif ?>
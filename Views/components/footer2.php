</div>

<?php loadJS('libraries/jQuery') ?>

<?php if($data): ?>

<noscript>
<h4 style="text-align:center">You must active javascript for this webpage</h4>
</noscript>

<script>

(function(){

    function check(element, from = false, k = 'key'){
        let key = element.dataset[k];//get the key
        let r = typeof key != 'undefined' && (from == false || key == from);
        if(r == true){
            let i = element.dataset['if'];
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
        element.removeAttribute('data-key');
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
        console.log(val);
        element.innerHTML = val;
        clearData(element);
        for(let a = 0;a<arr.length;a++){
            element.removeAttribute('data-'+arr[a]);
        }
    }

    function clearData(element){
        element.removeAttribute('data-foreach');
        element.removeAttribute('data-foreach-limit');

        element.removeAttribute('data-key');
        element.removeAttribute('data-global-key');
        
        element.removeAttribute('data-explore');
        element.removeAttribute('data-if')

        if(element.classList.contains('data')){
            element.classList.remove('data');
            element.classList.add('data-loaded');
        }
    }

    function load(element,arr,children = false){
        if(children){
            if(check(element) != false){
                load(element,arr);
            }else if(check(element,false,'global-key')){
                load(element,json[check(element,false,'global-key')]);
            }else{
                Array.from(element.children).forEach(function(e){
                    load(e,arr,true);
                });
            }
        }else{

            let f;
            if(f = check(element,false,'foreach')){//if there is a foreach value
                let children = element.cloneNode(true);//clone the children
                element.innerHTML = "";//clear the element
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
                    element.removeAttribute('data-foreach-limit');
                }
                for(let a = parseInt(n);a<parseInt(len);a += parseInt(i)){//foreach value append the children
                    Array.from(children).forEach(function(element){
                        if(check(element,f)){
                            load(element,arr[a]);
                        }
                        if(!check(element)){
                            load(element,arr[a],true);
                        }
                    });
                    element.appendChild(children.cloneNode(true));//append new children
                }
            }else if(f = check(element,false,'explore')){//if there is an explore value
                if(check(element)){
                    Array.from(element.children).forEach(function(e){
                        load(e,arr);
                    });
                }else{
                    Array.from(element.children).forEach(function(e){
                        load(e,arr[f]);
                    });
                }
            }else{//else
                let p = element.dataset.property;
                if(p = check(element,false,'property')){
                    update(element,arr[p],['property']);
                }else{
                    update(element,arr);
                }
            }

            if(check(element, false, 'onload')){
                element.innerHTML(element.dataset.onload);
                element.removeAttribute('data-onload');
            }

        }

        clearData(element);

    }

    let json;
    let page = document.querySelector('#page').cloneNode(true).innerHTML;

    function request(){
        $.get("<?=url(\Pickle\Engine\Router::$ajaxurl)?>",function(data){
            json = JSON.parse(data);//parse data
            render();
        })
    }

    request()

    <?php if($refresh != false): ?>

    let i = setInterval(request,<?=$refresh?>);

    <?php endif ?>
    function render(){
        document.querySelector('#page').innerHTML = page;
        Array.from(document.querySelectorAll('.data')).forEach(function(element){
            let k;
            if(k = check(element)){
                load(element,json[k]);
            }else if(k = check(element,false,'global-key')){
                load(element,json[k]);
            }
        });
        Array.from(document.querySelectorAll('[data-onload]')).forEach(function(element){
            let action = check(element, false, 'onload');
            if(action != false){
                window[action](element);
            }
        });
    }

})();

</script>

<?php endif ?>
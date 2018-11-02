<?php //listen(); ?>

<div id="page">
    
    <h1 id="main-title" class="middle">Pickle</h1>
    
    <a href="<?=url('/home')?>">url</a>
    <a href="<?=route('home')?>">route</a>

    <style>
    .data{
        font-style:italic;
    }
    .data-loaded{
        font-style:normal;
    }
    </style>

    <div class="data" data-key="users" data-foreach="user" data-foreach-limit="2">
        <p style="display:inline-block">Nom:</p>
        <p data-key="user" data-property="name" style="display:inline-block">Name</p>
        <p style="display:inline-block">Email:</p>
        <p data-key="user" data-property="email" style="display:inline-block">Email</p>
        <hr>
    </div>

    <p class="data" data-key="hello"></p>
    <div class="data" data-key="hello" data-if="hello"></div>

    <div class="data" data-key="users" data-foreach="user" data-foreach-limit="2:4">
        <div class="one">
            <div class="two">
                <div class="three">
                    <div data-key="user" data-property="name" data-transform="up"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="data" data-key="data" data-explore="data">
        <div class="data" data-global-key="users"></div>
        <div data-explore="user">
            <div data-property="name">name..</div>
        </div>
    </div>

    <div data-onload="double">eee</div>

    <script>
        function up(x){
            return x.toUpperCase();
        }
        function double(element){
            let a = element.innerHTML;
            element.innerHTML = a + a + a;
        }
    </script>

</div>

<?php fillTemplate('default', ['title' => $title, 'styles' => ['home']]); ?>
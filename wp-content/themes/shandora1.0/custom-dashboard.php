<style type="text/css">
  div.col-md-6.content {
    min-height: 500px;
    }
    iframe {
      -webkit-box-shadow: 0px 0px 15px 0px #323232;
      border: 10px solid #FFF;
      }
      .content img {
    width: 100%;
    }
    .wrap h2 {
        display:none;
    }
</style>
<link rel="stylesheet" type="text/css" href="http://ecotemporadas.com/wp-content/themes/shandora1.0/assets/bootstrap.css" media="screen" />

<body>
  <div class="row">
  <div class="col-md-8 video">
      <div class="videoWrapper">
          <iframe src='https://onedrive.live.com/embed?cid=3FF0BE26AF9B815D&resid=3FF0BE26AF9B815D%21848&authkey=AF9flfvrWoGXFDE&em=2&wdAr=1.7777777777777777' width='610px' height='367px' frameborder='0'></iframe>
      </div>
    </div>
    <div class="col-md-4 content">
      <div class="row">
        <div class="col-md-12 col-xs-12">
          <p style="font-size: 16px">Primeiro você deve cadastrar seu <b><a href="http://ecotemporadas.com/wp-admin/post-new.php?post_type=agent">Perfil ECO</b></a><br>
            São as informações que seu cliente precisa para entrar <br>em contato com você.</p>

            <p>Dica: Seja honesto para aumentar sua credibilidade.</p>

            <p style="font-size: 16px">Logo após, crie seu anúncio, vinculando o seu <br><b><a href="http://ecotemporadas.com/wp-admin/post-new.php?post_type=agent">Perfil ECO</b></a> com ele. <br><br>
            Deixe-o bem bonito e completo, </br>com isso fechará bons negócios!</p>

            <p>Dica: Insira boas fotos, isso chama a atenção :)</p>
        </div>
        <div class="col-md-6 col-xs-6">
          <a href="http://ecotemporadas.com/saiba-como-destacar-seu-anuncio/" target="_blank">
            <img src="http://ecotemporadas.com/wp-content/themes/shandora1.0/assets/theme/eco-button3_eax1.jpg" alt="" /></a>
        </div>
        <div class="col-md-6 col-xs-6">
          <a href="http://ecotemporadas.com/perguntas-frequentes/" target="_blank">
            <img src="http://ecotemporadas.com/wp-content/themes/shandora1.0/assets/theme/eco-button4_eax1.jpg" alt="" /></a>
        </div>
      </div>
    </div>
    
</div>  
</body>

 <script>
      /* Thanks to CSS Tricks for pointing out this bit of jQuery
  http://css-tricks.com/equal-height-blocks-in-rows/
  It's been modified into a function called at page load and then each time the page is resized. One large modification was to remove the set height before each new calculation. */

  equalheight = function(container){

  var currentTallest = 0,
       currentRowStart = 0,
       rowDivs = new Array(),
       $el,
       topPosition = 0;
   $(container).each(function() {

     $el = $(this);
     $($el).height('auto')
     topPostion = $el.position().top;

     if (currentRowStart != topPostion) {
       for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
         rowDivs[currentDiv].height(currentTallest);
       }
       rowDivs.length = 0; // empty the array
       currentRowStart = topPostion;
       currentTallest = $el.height();
       rowDivs.push($el);
     } else {
       rowDivs.push($el);
       currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
    }
     for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
       rowDivs[currentDiv].height(currentTallest);
     }
   });
  }

  $(window).load(function() {
    equalheight('.row col-md-6');
  });


  $(window).resize(function(){
    equalheight('.row col-md-6');
  });


  </script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://ecotemporadas.com/wp-content/themes/shandora1.0/assets/jquery.fitvids.js" ></script>
<script>
  $(".video").fitVids();
</script>
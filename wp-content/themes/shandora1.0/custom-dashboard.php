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
      equalheight('.main article');
    });


    $(window).resize(function(){
      equalheight('.main article');
    });


    </script>
  <style>
  article {
    float: left;
    width: 23%;
    background: #ccc;
    margin: 10px 1%;
    padding: 1%;
  }

	.column {
		width: 90%;
	}
	.row {
		float: left;
		width: 100%;
	}
	.item {
	    float: left;
	}
	article {
	float: left;
	width: 23%;
	background: none;
	margin: 0;
	padding: 0;
	}
	article a img {
	width: 100%;
	}
	#wpbody-content > div.wrap > h2 {
	display: none;
	}


  @media all and (max-width: 900px) {
  article {
      width: 48%
    }
  </style>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras egestas mauris rhoncus est accumsan venenatis. Ut pellentesque elit ut enim lacinia, scelerisque hendrerit libero fringilla. Pellentesque adipiscing, dolor ut consequat congue, magna diam tempus felis, a tempus lorem mi in enim. Sed non egestas turpis. Pellentesque vulputate libero quam. Ut id tristique elit. Ut vulputate ac nisl rutrum dapibus. Fusce at bibendum lectus. Aenean cursus interdum augue vitae molestie. Nam dignissim lacinia dui quis rhoncus. Phasellus diam orci, tincidunt sit amet bibendum vitae, convallis at erat. Phasellus lorem enim, luctus nec sapien ac, commodo consectetur odio. Nam in sapien metus. Nullam molestie semper rutrum. Ut sed nisi eu nisl egestas viverra in egestas dolor.</p>
<section class="main">
  <article><a href="
  http://ecotemporadas.com/informacoes-ao-anunciante/#criaranuncio" target="_blank"><img src="https://trello-attachments.s3.amazonaws.com/525813762c0bfe3c1300254f/52a623d2d02262322e0063db/eb3c3a2da09f8ca8e311d561ec741480/botao1.jpg" alt="" /></a></article>
  <article><a href="
  http://ecotemporadas.com/informacoes-ao-anunciante/#melhoreanuncio" target="_blank"><img src="https://trello-attachments.s3.amazonaws.com/525813762c0bfe3c1300254f/52a623d2d02262322e0063db/cfd7a01aae335c145d1ef3db13792454/botao2.jpg" alt="" /></a></article>
  <article><a href="
  http://ecotemporadas.com/informacoes-ao-anunciante/#oqueperfileco" target="_blank"><img src="https://trello-attachments.s3.amazonaws.com/525813762c0bfe3c1300254f/52a623d2d02262322e0063db/88b34b0cdf782dfa814cc70c5f972363/botao3.jpg" alt="" /></a></article>
  <article><a href="
  http://ecotemporadas.com/perguntas-frequentes/" target="_blank"><img src="https://trello-attachments.s3.amazonaws.com/525813762c0bfe3c1300254f/52a623d2d02262322e0063db/5d23404d8dde83f31743f026d149c190/botao4.jpg" alt="" /></a></article>
</section>
<div class="row">
	    <div class="videoWrapper">
		    <iframe src="//player.vimeo.com/video/25924530?portrait=0" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
		</div>
	</div>
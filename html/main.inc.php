<?php

$error = false;
$error_message = array();

$email = '';
$sent = null;

if (isset($_GET['sent'])) {

   $sent = isset($_GET['sent']) ? $_GET['sent'] : null;

   if ($sent === 'success') {
      $sent = 'success';
   } else {
      $sent = 'fail';
   }
}

$exchangeinfo = new exchangeinfo();
$res = $exchangeinfo->get();


include_once("../html/common/header.inc.php");

auth::newAuthenticityToken();

$page_id = "main";

?>


<body>
   <div class="allButFooter">
      <?php

      include_once("../html/common/topbar.inc.php");

      ?>

      <br><br><br><br>

      <div class="container" id="container" style="padding-top:80px;">

         <div class="row justify-content-md-between">
            <div class="col-lg-3" id="sakrij">
               <p class="text-center"><b>Kupujem</b></p>
            </div>
            <div class="col-lg-3" id="sakrij">
               <p class="text-center"><b>Prodajem</b></p>
            </div>
         </div>

         <div class="row justify-content-md-center">

            <div class="col-lg-4" id="fiat">
               <label class="lbl" for="options">Fiat</label>
               <select id="options">
               </select>

               <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="0.00" min="0" id="number-balance_fiat" required="required" aria-required="true" aria-label="Cryptocurrency" aria-describedby="basic-addon2">
                  <div class="input-group-append">
                     <span class="input-group-text fiat_currency">___</span>
                  </div>
               </div>

            </div>

            <div class="col-lg-4" id="switch_btn">
               <a href="#"><img id="switch_img" class="switch" width="70" height="70" src="img/swap2.png" onmouseover="this.src='img/swap.png'" onmouseout="this.src='img/swap2.png'" onclick="switchContent()"> </a>
            </div>

            <div class="col-lg-4" id="kripto">
               <label class="lbl" for="options">Crypto</label>
               <select id="options2">
               </select>

               <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="0.00" min="0" id="number-balance_crypto" required="required" aria-required="true" aria-label="Cryptocurrency" aria-describedby="basic-addon2">
                  <div class="input-group-append">
                     <span class="input-group-text crypto_currency">___</span>
                  </div>
               </div>

            </div>
         </div>

      </div>

   </div>

   <script type="text/javascript">
      /**
       * TODO
       * jos ima dosta rubni uvjeta,
       * što ako stisne switch ili promine coin, trebale bi se vrijednosti updatati a trenutno se samo
       * dodat description polje u ddData koje sadrzava vrijednost valute
       * nepotrebna 2 arraya, coins i tecajevi zamijenit jednim
       */

      //Dynamically build coin list

      var js_data_coins = '<?php echo json_encode($exchangeinfo->getCoins()); ?>';
      var coins = JSON.parse(js_data_coins);

      const ddDataFiat = [{
         text: "HRK",
         value: "HRK",
         selected: false,
         imageSrc: "img/bank.svg"
      }];

      const ddDataCrypto =
         coins.map((element, key) => ({
            text: `${element}`,
            value: `${element}`,
            selected: false,
            imageSrc: `img/${element.toLowerCase()}.svg`
         }));


      let fiat_currency;
      let crypto_currency;
      let state = true;

      $('#options').ddslick({
         data: ddDataFiat,
         defaultSelectedIndex: 0,
         height: 0,
         onSelected: (data) => {
            fiat_currency = $('#options').data('ddslick').selectedData.value;
            $(".fiat_currency").text(fiat_currency);
         }
      });

      $('#options2').ddslick({
         data: ddDataCrypto,
         defaultSelectedIndex: 0,
         onSelected: (data) => {
            crypto_currency = $('#options2').data('ddslick').selectedData.value;
            $(".crypto_currency").text(crypto_currency);
         }

      });;

      function switchContent() {
         state = !state;
         if (state)
            $("#kripto").before($("#fiat")).before($("#switch_btn"));
         else
            $("#fiat").before($("#kripto")).before($("#switch_btn"));
      }

      // Calculating value

      var js_data = '<?php echo json_encode($res); ?>';
      var tecajevi = JSON.parse(js_data);
      console.log(tecajevi);

      $("#number-balance_fiat").on("input", (e) => {

         const value = e.target.value;
         let tecaj;

         if (state)
            tecaj = tecajevi.items.find(element => element['key'] === crypto_currency + '_BUY')['value'] * 1;
         else
            tecaj = tecajevi.items.find(element => element['key'] === crypto_currency + '_SELL')['value'] * 1;

         $("#number-balance_crypto").val(value / tecaj);

      });

      $("#number-balance_crypto").on("input", (e) => {
         const value = e.target.value;
         let tecaj;
         if (state)
            tecaj = tecajevi.items.find(element => element['key'] === crypto_currency + '_SELL')['value'] * 1;
         else
            tecaj = tecajevi.items.find(element => element['key'] === crypto_currency + '_BUY')['value'] * 1;

         $("#number-balance_fiat").val(value * tecaj);
      });
   </script>

   <?php

   include_once("../html/common/footer.inc.php");

   ?>
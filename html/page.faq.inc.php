<?php

include_once("../html/common/header.inc.php");

?>

<body>
  <div class="allButFooter">
    <?php

    include_once("../html/common/topbar.inc.php");

    ?>

    <section class="page-section bg-primary text-black mb-0" id="about">

      <div class="container">


        <details open>
          <summary>Kako se računaju tečajevi?</summary>
          <div class="faq__content">
            <p>Tečajevi se računaju pomoću Binance i HNB vrijednosti.</p>
          </div>
        </details>
        <details>
          <summary>Koliko su ažurni rezulati?</summary>
          <div class="faq__content">
            <p>Maximalna starost podatak je 2 minute.</p>
          </div>
        </details>
        <details>
          <summary>Jeli postoji mogućnost implementacije ovoga kao API servisa?</summary>
          <div class="faq__content">
            <p>Javi se, dogovorit ćemo se.</p>
          </div>
        </details>

      </div>

    </section>
  </div>

  <?php

  include_once("../html/common/footer.inc.php");

  ?>
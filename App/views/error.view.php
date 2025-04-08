<?php
// We use helper function to import components
// imports head 
loadPartial("head");
// imports navbar
loadPartial("navbar");

?>

<section>
  <div class="container mx-auto p-4 mt-4">
    <div
      class="text-center text-3xl mb-4 font-bold border border-gray-300 p-3">
      <?= $status ?>
    </div>
    <p class="text-center text-2xl mb-4"><?= $message ?>
    <div class="text-center font-bold">
      <a href="/listings" class="px-2 py-1 w-[250px] bg-blue-900 text-white">Go Back To Listings</a>
    </div>
  </div>
</section>



<?php

// imports footer component with closing body and html tags
loadPartial("footer");
?>
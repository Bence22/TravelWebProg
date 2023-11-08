<?php
require __DIR__ . '/../hotels/model/HotelModel.php';
$hotel_list_model = new hotels\model\HotelModel();
?>

<footer>
  <section id="rooms">
    <h2>Our Rooms</h2>
  <?php
  
    $hotels = $hotel_list_model->list();
    if (!empty($hotels)) {
      echo $hotels;
    } else {
  ?>
    <div class="room">
      <img src="assets/images/room1.jpg" alt="Room 1">
      <h3>Standard Room</h3>
      <p>Enjoy a comfortable stay in our standard rooms.</p>
    </div>
    <div class="room">
      <img src="assets/images/room2.jpg" alt="Room 2">
      <h3>Deluxe Room</h3>
      <p>Experience luxury in our deluxe rooms with a view.</p>
    </div>
  <?php
  }
  ?>

  <div class="flex row">
    <section id="amenities">
      <h2>Amenities</h2>
      <ul class="amenity-list">
        <li>Swimming Pool</li>
        <li>Restaurant</li>
        <li>Spa and Wellness</li>
        <li>Conference Rooms</li>
      </ul>
    </section>

    <section id="contact">
      <h2>Contact Us</h2>
      <p>If you have any questions or would like to make a reservation, please contact us.</p>
      <address>
        <p>Napfeny Tours</p>
        <p>123 Main Street</p>
        <p>City, Country</p>
      </address>
      <p class="contact-email">Email: info+napfeny_tours@example.com</p>
      <p class="contact-phone">Phone: +123-456-7890</p>
    </section>
  </div>
  <p class="copyright">&copy; <?php echo date("Y"); ?> Hotel Napfeny tours</p>
</footer>
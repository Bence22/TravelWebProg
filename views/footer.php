<?php
require __DIR__ . '/../hotels/model/HotelModel.php';
$hotel_list_model = new hotels\model\HotelModel();
?>

<footer>
  <div class="blocks flex row">
    <section id="amenities">
      <h2>Amenities</h2>
      <div class="container">
        <ul class="amenity-list">
          <li>Swimming Pool</li>
          <li>Restaurant</li>
          <li>Spa and Wellness</li>
          <li>Conference Rooms</li>
        </ul>
      </div>
    </section>

    <section id="contact">
      <h2>Contact Us</h2>
      <div class="container">
        <p>If you have any questions or would like to make a reservation, please contact us.</p>
        <address>
          <p>Napfeny Tours</p>
          <p>123 Main Street</p>
          <p>City, Country</p>
        </address>
        <p class="contact-email">Email: info+napfeny_tours@example.com</p>
        <p class="contact-phone">Phone: +123-456-7890</p>
      </div>
    </section>
  </div>
  <p class="copyright">&copy; <?php echo date("Y"); ?> Hotel Napfeny tours</p>
</footer>
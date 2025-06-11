<?php
include("dbconn.php");
ob_start();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<div id="home">
    <div class="header__container">
        <div class="header__content">
            <p>ELEVATE YOUR TRAVEL JOURNEY</p>
            <h1>Experience The Magic Of Flight!</h1>
            <div class="header__btns">
                <button class="btn" onclick="window.location.href='booking.php'">Book A Trip Now</button>
                <a href="https://www.youtube.com/watch?v=J342qeHrMSQ" target="_blank" rel="noopener">
                    <span><i class="ri-play-circle-fill"></i></span>
                </a>
            </div>
        </div>
        <div class="header__image">
            <img src="photos/plane.png" alt="header" />
        </div>
    </div>
</div>

<section class="section__container destination__container" id="about">
    <h2 class="section__header">Popular Destination</h2>
    <p class="section__description">
        Discover the Most Loved Destinations Around the Globe
    </p>
    <div class="destination-slider" style="position:relative;overflow:hidden;">
        <button id="slide-left" style="position:absolute;left:-40px;top:50%;transform:translateY(-50%);z-index:2;">&#8592;</button>
        <div class="destination__grid" id="destinationGrid" style="display:flex;overflow-x:auto;scroll-behavior:smooth;">
            <?php
            $destinations = [
                ["img"=>"photos/destination-1.jpg","title"=>"Tradition and Futurism","place"=>"New York City, USA"],
                ["img"=>"photos/destination-2.jpg","title"=>"The City of Lights","place"=>"Paris, France"],
                ["img"=>"photos/destination-3.jpg","title"=>"Island of the Gods","place"=>"Bali, Indonesia"],
                ["img"=>"photos/destination-4.jpg","title"=>"Tokyo Adventure","place"=>"Tokyo, Japan"],
                ["img"=>"photos/destination-5.jpg","title"=>"Sydney Opera","place"=>"Sydney, Australia"],
                ["img"=>"photos/destination-6.jpg","title"=>"London Bridge","place"=>"London, UK"],
                ["img"=>"photos/destination-7.jpg","title"=>"Roman Holiday","place"=>"Rome, Italy"],
                ["img"=>"photos/destination-8.jpg","title"=>"Cape Town Views","place"=>"Cape Town, South Africa"],
                ["img"=>"photos/destination-9.jpg","title"=>"Dubai Skyscrapers","place"=>"Dubai, UAE"]
            ];
            foreach ($destinations as $d) {
                echo '<div class="destination__card" style="min-width:250px;margin-right:20px;">
                        <img src="'.$d['img'].'" alt="destination" />
                        <div class="destination__card__details">
                            <div>
                                <h4>'.$d['title'].'</h4>
                                <p>'.$d['place'].'</p>
                            </div>
                            <div class="destination__rating">
                                <span><i class="ri-star-fill"></i></span>
                                4.7
                            </div>
                        </div>
                        <button class="btn" onclick="window.location.href=\'booking.php?destination='.urlencode($d['place']).'\'">Book Now</button>
                    </div>';
            }
            ?>
        </div>
        <button id="slide-right" style="position:absolute;right:-40px;top:50%;transform:translateY(-50%);z-index:2;">&#8594;</button>
    </div>
</section>
<script>
const grid = document.getElementById('destinationGrid');
document.getElementById('slide-left').onclick = () => grid.scrollBy({left: -300, behavior: 'smooth'});
document.getElementById('slide-right').onclick = () => grid.scrollBy({left: 300, behavior: 'smooth'});
</script>
<?php
$content = ob_get_clean();
$title = "Register";
include 'templates/home_header.php';
?>
